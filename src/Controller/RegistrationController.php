<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {
    }
    public function __invoke(Request $request): JsonResponse
    {
        $params = json_decode($request->getContent(), true);
        // Crear una nueva instancia de la entidad User
        $user = new User();
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $params['password']
        );
        $user->setPassword($hashedPassword);
        $user->setUsername($params['username']);
        if (array_key_exists('email', $params))
            $user->setEmail($params['email']);
        // Guardar el usuario en la base de datos
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(["username" => $user->getUsername(), "email" => $user->getEmail(), "roles" => $user->getRoles()]);
    }
}
