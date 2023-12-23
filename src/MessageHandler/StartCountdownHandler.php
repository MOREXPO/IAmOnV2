<?php

namespace App\MessageHandler;

use App\Message\StartCountdown;
use App\Repository\SwitchesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StartCountdownHandler
{
    public function __construct(

        private SwitchesRepository $switchesRepository,
        private EntityManagerInterface $entityManager

    ) {
    }

    public function __invoke(StartCountdown $message)
    {
        $countdownId = $message->getCountdownId();

        $switch = $this->switchesRepository->find($countdownId);
        if ($switch->isState()) {
            $switch->setClickDateEnd(new DateTime());
            $switch->setState(false);
        }
        $this->entityManager->persist($switch);

        $this->entityManager->flush();
    }
}
