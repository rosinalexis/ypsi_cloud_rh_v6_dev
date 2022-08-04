<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\UserConfirmation;
use App\Service\UserConfirmationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class UserConfirmationDataPersister implements ContextAwareDataPersisterInterface
{
    private  UserConfirmationService $userConfirmationService;

    public function __construct(UserConfirmationService $userConfirmationService)
    {
        $this->userConfirmationService =$userConfirmationService;
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

            // traitement via un service
            $this->userConfirmationService->confirmUser($data->getConfirmationToken(),$data->getPassword());

            // réponse à utilisateur
            return new JsonResponse(null, Response::HTTP_ACCEPTED);
        }
    }

    public function remove($data, array $context = [])
    {
    }
}