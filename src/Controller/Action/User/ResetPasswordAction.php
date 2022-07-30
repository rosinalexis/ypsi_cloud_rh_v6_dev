<?php

namespace App\Controller\Action\User;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsController]
class ResetPasswordAction extends AbstractController
{

    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $em;
    private JWTTokenManagerInterface $tokenManager;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        JWTTokenManagerInterface $tokenManager
    )
    {
        $this->validator = $validator;
        $this->passwordHasher =$passwordHasher;
        $this->em =$em;
        $this->tokenManager =$tokenManager;
    }

    public function __invoke(User $data): JsonResponse
    {

        // validation du mot de passe
        $this->validator->validate($data,['groups'=> ['put:reset:password']]);

        // hash du nouveau mot de passe
        $data->setPassword(
            $this->passwordHasher->hashPassword(
                $data,
                $data->getNewPassword()
            )
        );

        $data->setPasswordChangeDate(time());

        //modification dans la base
        $this->em->flush();

        $token = $this->tokenManager->create($data);


        return new JsonResponse(['token' => $token]);

    }
}