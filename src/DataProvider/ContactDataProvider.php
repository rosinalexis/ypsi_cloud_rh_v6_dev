<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Contact;
use App\Entity\User;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class ContactDataProvider implements ContextAwareCollectionDataProviderInterface,RestrictedDataProviderInterface,ItemDataProviderInterface
{

    private Security $security;
    private ContactRepository $contactRepository;

    public function __construct(Security $security,ContactRepository $contactRepository)
    {
        $this->security =$security;
        $this->contactRepository =$contactRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Contact::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        $companyId = $user->getCompany()->getId();

        return $this->contactRepository->findByCompany($companyId);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Contact|null
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        $searchContact = $this->contactRepository->find($id);

        if($searchContact === null)
        {
            throw new NotFoundHttpException("Contact is not found.");
        }

        if(($user->getCompany()->getId() === $searchContact->getCompanyId()))
        {
            return $searchContact;
        }

        if(($user->getCompany()->getId() !== $searchContact->getCompanyId()))
        {
            throw new HttpException(Response::HTTP_FORBIDDEN,"This contact is not in your company.");
        }

        return null;
    }


}