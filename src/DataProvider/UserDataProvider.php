<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface,RestrictedDataProviderInterface,ItemDataProviderInterface
{
    private Security $security;
    private UserRepository $userRepository;

    public function __construct(Security $security,UserRepository $userRepository)
    {
        $this->security =$security;
        $this->userRepository =$userRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /**
         * @var $user User
         */
       $user = $this->security->getUser();

       return $user->getCompany()->getUsers();
    }


    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): object|null
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        $searchUser = $this->userRepository->find($id);


        if($searchUser === null){
            throw  new NotFoundHttpException("User is not found.");
        }

        if(($user->getCompany()->getId() === $searchUser->getCompany()->getId())){
            return $searchUser;
        }

        if($user->getCompany()->getId() !== $searchUser->getCompany()->getId()){

            throw new HttpException(Response::HTTP_FORBIDDEN,"This user is not in your company.");
        }

        return null;
    }
}