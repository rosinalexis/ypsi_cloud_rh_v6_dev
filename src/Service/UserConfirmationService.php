<?php

namespace App\Service;

use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserConfirmationService
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private  UserPasswordHasherInterface $passwordHasher;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger
    )
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->passwordHasher =$passwordHasher;
        $this->logger = $logger;
    }


    public function confirmUser(string $confirmationToken, $plainPassword): void
    {
        $this->logger->debug('Search user by confirmation token.');

        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        // si le token n'existe pas
        if (!$user) {

            $this->logger->debug(' User by confirmation token is not found.');

            throw new InvalidConfirmationTokenException();
        }

        // si le token existe
        $user->setConfirmed(true);
        $user->setConfirmationToken(null);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        // enregistrement en base
        $this->em->flush();

        $this->logger->debug('Confirmed User by confirmation token.');
    }

    public function confirmToken(string $confirmationToken): bool
    {
        $this->logger->debug('Search confirmation token in User.');

        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        // si le token n'existe pas
        if (!$user) {

            $this->logger->debug('Confirmation token is not found.');

            throw new InvalidConfirmationTokenException();
        }

        $this->logger->debug('Confirmed Token exist.');

        return true;
    }

}