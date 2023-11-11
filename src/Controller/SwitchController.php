<?php

namespace App\Controller;

use App\Entity\Switches;
use App\Entity\UserSwitch;
use App\Message\StartCountdown;
use App\Repository\SwitchesRepository;
use App\Repository\UserSwitchRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Uid\Uuid;
use PDO;

class SwitchController extends AbstractController
{
    #[Route('/switch/{uuid}', name: 'app_switch')]
    public function index(Uuid $uuid, SwitchesRepository $switchesRepository): Response
    {
        $switch = $switchesRepository->findOneBy([
            'privateUri' => $uuid
        ]);
        if ($switch) $isPublic = false;
        else {
            $switch = $switchesRepository->findOneBy([
                'publicUri' => $uuid
            ]);
            $isPublic = true;
        }
        $is_suscrito = false;
        if ($this->getUser()) {
            foreach ($this->getUser()->getSuscriptions() as $suscription) {
                $is_suscrito = $suscription->getSwitch()->getId() == $switch->getId();
            }
        }

        return $this->render('switch/index.html.twig', [
            'controller_name' => 'SwitchController',
            'switch' => $switch->toJson(),
            'isPublic' => $isPublic,
            'is_suscrito' => $is_suscrito,
        ]);
    }

    #[Route('/create-switch', name: 'app_create_switch')]
    public function createSwitch(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $switch = new Switches();
        $switch->setName($request->get('nombre'));
        $switch->setDescription($request->get('descripcion'));
        $switch->setOwner($this->getUser());

        $entityManager->persist($switch);

        $entityManager->flush();

        return $this->redirectToRoute("app_index");
    }

    #[Route('/remove-switches/{id}', name: 'switches_delete', methods: ['GET'])]
    public function deleteSwitch(int $id, EntityManagerInterface $entityManager, SwitchesRepository $switchesRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $switch = $switchesRepository->find($id);

        $entityManager->remove($switch);
        $entityManager->flush();

        // Redirige o responde de acuerdo a tus necesidades.
        // Por ejemplo, puedes redirigir a una página de lista de Switches o enviar una respuesta JSON.

        return $this->redirectToRoute('app_index'); // Redirige a la lista de Switches
    }

    #[Route('/check-switch/{id}', name: 'app_check_switch', methods: ['POST'])]
    public function checkSwitch(int $id, MailerInterface $mailer, Request $request, SwitchesRepository $switchesRepository, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): JsonResponse
    {
        $switch = $switchesRepository->find($id);
        $state =  filter_var($request->request->get("isChecked"), FILTER_VALIDATE_BOOLEAN);
        $onTime = $request->request->get("onTime");
        if ($state) {
            $countdownId = $switch->getId();
            $message = new StartCountdown($countdownId, $onTime * 60000);
            $delayStamp = new DelayStamp($onTime * 60000);
            $messageBus->dispatch($message, [$delayStamp]);
            $switch->setClickDateStart(new DateTime());
            $switch->setState(true);

            foreach ($switch->getFollowers() as $follower) {
                $user = $follower->getUser();
                if (!empty($user->getEmail())) {
                    $email = (new Email())
                        ->from('prueba@gmail.com')
                        ->to($user->getEmail())
                        //->cc('cc@example.com')
                        //->bcc('bcc@example.com')
                        //->replyTo('fabien@example.com')
                        //->priority(Email::PRIORITY_HIGH)
                        ->subject('AVISO SWITCH SUSCRITO ENCENDIDO (' . $switch->getName() . ')')
                        ->text('Tienes el switch ' . $switch->getName() . ' encendido')
                        ->html('<p>Se encendio a las ' . $switch->getClickDateStart()->format('Y-m-d H:i:s') . '</p>');

                    $mailer->send($email);
                }
            }
        } else {
            $switch->setClickDateEnd(new DateTime());
            $switch->setState(false);
        }



        $entityManager->persist($switch);

        $entityManager->flush();

        return new JsonResponse(["message" => "Cambio realizado"]);
    }

    #[Route('/change-follower-switch/{id}', name: 'app_change_follower_switch', methods: ['POST'])]
    public function addFollowerSwitch(int $id, Request $request, SwitchesRepository $switchesRepository, UserSwitchRepository $userSwitchRepository, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $switch = $switchesRepository->find($id);
        $userSwitch = $userSwitchRepository->findOneBy(['user' => $this->getUser(), 'switch' => $switch]);
        if ($userSwitch) {
            $switch->removeFollower($userSwitch);
            $entityManager->remove($userSwitch);
            $entityManager->flush();
        } else {
            $userSwitch = new UserSwitch();
            $userSwitch->setUser($this->getUser());
            $userSwitch->setSwitch($switch);
            $switch->addFollower($userSwitch);

            $entityManager->persist($userSwitch);
            $entityManager->persist($switch);
        }


        $entityManager->flush();
        if ($request->request->get("switch_uuid")) {
            return $this->redirectToRoute("app_switch", ['uuid' => $request->request->get("switch_uuid")]);
        } else {
            return $this->redirectToRoute("app_index");
        }
    }
}
