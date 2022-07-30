<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Entity\UserConfirmation;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserConfirmationDataPersister implements ContextAwareDataPersisterInterface
{

    private  UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->entityManager =$entityManager;
        $this->passwordHasher =$passwordHasher;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof UserConfirmation;
    }

    /**
     * @param $data UserConfirmation
     * @param array $context
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        if ($data instanceof UserConfirmation && (($context['collection_operation_name'] ?? null) === 'post')) {

            $user = $this->userRepository->findOneBy(['confirmationToken' => $data->getConfirmationToken()]);

            // si le token n'existe pas
            if (!$user) {
                throw new NotFoundHttpException("Not found.");
            }

            // si le token existe
            $user->setConfirmed(true);
            $user->setConfirmationToken(null);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data->getPassword()));

            // enregistrement en base
            $this->entityManager->flush();

            // reponse utilisateur
            return new JsonResponse(null, Response::HTTP_ACCEPTED);
        }
    }

    public function remove($data, array $context = [])
    {
    }
}