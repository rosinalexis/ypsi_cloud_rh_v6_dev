<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Company;
use App\Entity\User;
use App\Repository\CompanyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class CompanyDataProvider implements RestrictedDataProviderInterface,ItemDataProviderInterface
{

    private Security $security;
    private CompanyRepository $companyRepository;

    public function __construct(Security $security,CompanyRepository $companyRepository)
    {
        $this->security =$security;
        $this->companyRepository =$companyRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Company::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Company|null
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        $searchCompany = $this->companyRepository->find($id);

        if($searchCompany === null){
            throw  new NotFoundHttpException("Company is not found.");
        }

        if(($user->getCompany()->getId() === $searchCompany->getId())){
            return $searchCompany;
        }

        if(!($user->getCompany()->getId() === $searchCompany->getId())){

            throw new HttpException(Response::HTTP_FORBIDDEN,"This company is not your company.");
        }

        return null;
    }


}