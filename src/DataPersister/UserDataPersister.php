<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $_em;
    private UserPasswordHasherInterface $_passwordHaser;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->_em = $em;
        $this->_passwordHaser =$passwordHasher;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
        dd($data);

        if ($data instanceof User && (($context['collection_operation_name'] ?? null) === 'post')) {

            $this->hashUserPassword($data);
        }

        //enregistrement des données
        $this->_em->persist($data);
        $this->_em->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->_em->remove($data);
        $this->_em->flush();
    }

    private function hashUserPassword(User $user)
    {
        // hash du mot de passe de l'utilisateur
        $user->setPassword(
            $this->_passwordHaser->hashPassword(
                $user,
                $user->getPassword()
            )
        );

    }
}