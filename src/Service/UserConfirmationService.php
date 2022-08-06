<?php

namespace App\Service;

use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserConfirmationService
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private  UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->passwordHasher =$passwordHasher;
    }


    public function confirmUser(string $confirmationToken, $plainPassword): void
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        // si le token n'existe pas
        if (!$user) {
            throw new InvalidConfirmationTokenException();
        }

        // si le token existe
        $user->setConfirmed(true);
        $user->setConfirmationToken(null);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        // enregistrement en base
        $this->em->flush();
    }

    public function confirmToken(string $confirmationToken): bool
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        // si le token n'existe pas
        if (!$user) {
            throw new InvalidConfirmationTokenException();
        }

        return true;
    }

}