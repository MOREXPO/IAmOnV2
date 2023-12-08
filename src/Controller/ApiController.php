<?php

namespace App\Controller;

use App\Entity\Switches;
use App\Entity\User;
use App\Repository\SwitchesRepository;
use App\Repository\UserRepository;
use DateTime;
use App\Message\StartCountdown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/listarSwitches', name: 'listarSwitches')]
    public function listarSwitches(Request $request, SwitchesRepository $switchesRepository, UserRepository $userRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $filter = $request->get('filter');
        $mis_switches = $switchesRepository->findBy([
            "owner" => $this->getUser(),
        ]);
        if ($filter) {
            $filtered_switches = array_filter($mis_switches, function ($switch) use ($filter) {
                $switchName = $switch->getName();
                return strpos($switchName, $filter) !== false || $switchName === $filter;
            });

            $mis_switches = array_values($filtered_switches);
        }
        $subscribed_switches = $this->getUser()->getSuscriptions();
        $subscribed_switches_aux = [];
        foreach ($subscribed_switches as $switch) {
            $switchName = $switch->getSwitch()->getName();
            if ($filter) {
                if (strpos($switchName, $filter) !== false) {
                    $subscribed_switches_aux[] = $switch->getSwitch()->toJson();
                }
            } else {
                $subscribed_switches_aux[] = $switch->getSwitch()->toJson();
            }
        }
        $subscribed_switches = $subscribed_switches_aux;
        $mis_switches_aux = [];
        foreach ($mis_switches as $switch) {
            $mis_switches_aux[] = $switch->toJson();
        }
        $mis_switches = $mis_switches_aux;

        return new JsonResponse([
            'mis_switches' => $mis_switches,
            'subscribed_switches' => $subscribed_switches,
            'filter' => $filter
        ]);
    }

    #[Route('/switch/{uuid}', name: 'api_switch')]
    public function switch (Uuid $uuid, SwitchesRepository $switchesRepository): JsonResponse
    {
        $switch = $switchesRepository->findOneBy([
            'privateUri' => $uuid
        ]);
        if ($switch)
            $isPublic = false;
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

        return new JsonResponse([
            'switch' => $switch->toJson(),
            'isPublic' => $isPublic,
            'is_suscrito' => $is_suscrito,
        ]);
    }

    #[Route('/create-switch', name: 'api_create_switch')]
    public function createSwitch(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $switch = new Switches();
        $content = json_decode($request->getContent(), true);
        $switch->setName($content['nombre']);
        $switch->setDescription($content['description']);
        $switch->setOwner($this->getUser());

        $entityManager->persist($switch);

        $entityManager->flush();

        return new JsonResponse($switch->toJson());
    }

    #[Route('/remove-switches/{id}', name: 'api_switches_delete', methods: ['GET'])]
    public function deleteSwitch(int $id, EntityManagerInterface $entityManager, SwitchesRepository $switchesRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $switch = $switchesRepository->find($id);

        $entityManager->remove($switch);
        $entityManager->flush();

        // Redirige o responde de acuerdo a tus necesidades.
        // Por ejemplo, puedes redirigir a una página de lista de Switches o enviar una respuesta JSON.

        return new JsonResponse(["Switch eliminado con éxito"]); // Redirige a la lista de Switches
    }

    #[Route('/user_auth', name: 'user_auth', methods: ['GET'])]
    public function userAuth(): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        return new JsonResponse(["id" => $this->getUser()->getId(), "username" => $this->getUser()->getUsername(), "email" => $this->getUser()->getEmail(), "roles" => $this->getUser()->getRoles()]); // Redirige a la lista de Switches
    }

    #[Route('/check-switch/{id}', name: 'api_check_switch', methods: ['POST'])]
    public function checkSwitch(int $id, MailerInterface $mailer, Request $request, SwitchesRepository $switchesRepository, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): JsonResponse
    {
        $switch = $switchesRepository->find($id);
        $content = json_decode($request->getContent(), true);
        $state = filter_var($content['isChecked'], FILTER_VALIDATE_BOOLEAN);
        $onTime = $content['onTime'];
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

        return new JsonResponse(["state" => $switch->isState(), "message" => "Switch cambio al estado " . ($switch->isState() ? "true" : "false")]);
    }

    #[Route('/change-follower-switch/{id}', name: 'api_change_follower_switch', methods: ['POST'])]
    public function addFollowerSwitch(int $id, Request $request, SwitchesRepository $switchesRepository, UserSwitchRepository $userSwitchRepository, EntityManagerInterface $entityManager, MessageBusInterface $messageBus): JsonResponse
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
            return new JsonResponse(['uuid' => $request->request->get("switch_uuid")]);
        } else {
            return new JsonResponse(["No se ha encontrado Uri"]);
        }
    }

    #[Route('/registration', name: 'api_registration', methods: ['POST'])]
    public function api_registration(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        // Crear una nueva instancia de la entidad User
        $user = new User();
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $params['password']
        );
        $user->setPassword($hashedPassword);
        $user->setUsername($params['username']);
        if (array_key_exists('email', $params))
            $user->setEmail($params['email']);
        // Guardar el usuario en la base de datos
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(["username" => $user->getUsername(), "email" => $user->getEmail(), "roles" => $user->getRoles()]);
    }
}
