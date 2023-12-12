<?php

namespace App\Controller;

use App\Entity\UserSwitch;
use App\Repository\UserSwitchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\SwitchesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChangeSwitchFollowerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SwitchesRepository $switchesRepository,
        private UserSwitchRepository $userSwitchRepository,
    ) {
    }

    public function __invoke($id): JsonResponse
    {
        $switch = $this->switchesRepository->find($id);
        $userSwitch = $this->userSwitchRepository->findOneBy(['user' => $this->getUser(), 'switch' => $switch]);
        if ($userSwitch) {
            $switch->removeFollower($userSwitch);
            $this->entityManager->remove($userSwitch);
            $this->entityManager->flush();
        } else {
            $userSwitch = new UserSwitch();
            $userSwitch->setUser($this->getUser());
            $userSwitch->setSwitch($switch);
            $switch->addFollower($userSwitch);

            $this->entityManager->persist($userSwitch);
            $this->entityManager->persist($switch);
        }


        $this->entityManager->flush();
        return new JsonResponse($switch->toJson());
    }
}
