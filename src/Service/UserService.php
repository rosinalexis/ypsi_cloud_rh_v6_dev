<?php

namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private UserRepository $userRepository;
    private  EntityManagerInterface $entityManager;
    private  UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher =$passwordHasher;
        $this->userRepository = $userRepository;
        $this->validator =$validator;
    }

    public function createSuperAdminUser(string $email, string $plainPassword)
    {
        $status = false;
        $message = "This user already exists in the app or your email or password is not valid.";

        $userChecker = $this->userRepository->findOneBy(["email" => $email]);

        //ajouter l'utilisateur
        $newSuperAdminUser = new User();
        $newSuperAdminUser->setBlocked(false)
            ->setConfirmed(true)
            ->setEmail($email)
            ->setPassword($this->passwordHasher->hashPassword($newSuperAdminUser,$plainPassword))
            ->setRoles(User::ROLE_SUPER_ADMIN);

        $errors = $this->validator->validate($newSuperAdminUser,null,['user:post:write']);

        if ($userChecker || ($errors->count()>0))
        {
            $message = $message.' '. (string)$errors;
        }
        else{
            //enregistrement en base
            $this->entityManager->persist($newSuperAdminUser);
            $this->entityManager->flush();
            $status =true;
            $message = "user has been created";
        }

        return [
            'status'=> $status,
            'message' => $message
            ];
    }
}