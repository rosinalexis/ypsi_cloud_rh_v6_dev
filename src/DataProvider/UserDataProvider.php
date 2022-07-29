<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface,RestrictedDataProviderInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security =$security;
    }
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /**
         * @var $user User
         */
       $user = $this->security->getUser();

       return $user->getCompany()->getUsers();
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class;
    }
}