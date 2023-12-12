<?php

namespace App\Controller;

use App\Entity\Switches;
use App\Repository\SwitchesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Message\StartCountdown;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use DateTime;

class SwitchCheckController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SwitchesRepository $switchesRepository,
        private MessageBusInterface $messageBus
    ) {
    }

    public function __invoke($id, Request $request): JsonResponse
    {
        $switch = $this->switchesRepository->find($id);
        $content = json_decode($request->getContent(), true);
        $state = filter_var($content['isChecked'], FILTER_VALIDATE_BOOLEAN);
        $onTime = $content['onTime'];
        if ($state) {
            $countdownId = $switch->getId();
            $message = new StartCountdown($countdownId, $onTime * 60000);
            $delayStamp = new DelayStamp($onTime * 60000);
            $this->messageBus->dispatch($message, [$delayStamp]);
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

                    $this->mailer->send($email);
                }
            }
        } else {
            $switch->setClickDateEnd(new DateTime());
            $switch->setState(false);
        }



        $this->entityManager->persist($switch);

        $this->entityManager->flush();

        return new JsonResponse(["state" => $switch->isState(), "message" => "Switch cambio al estado " . ($switch->isState() ? "true" : "false")]);
    }
}
