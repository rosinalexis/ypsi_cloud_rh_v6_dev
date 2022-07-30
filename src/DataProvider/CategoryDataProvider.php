<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class CategoryDataProvider implements ContextAwareCollectionDataProviderInterface,RestrictedDataProviderInterface,ItemDataProviderInterface
{
    private Security $security;
    private CategoryRepository $categoryRepository;

    public function __construct(Security $security,CategoryRepository $categoryRepository)
    {
        $this->security =$security;
        $this->categoryRepository =$categoryRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Category::class;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();
        $companyId = $user->getCompany()->getId();

        return $this->categoryRepository->findByCompany($companyId);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Category|null
    {
        /**
         * @var $user User
         */
        $user = $this->security->getUser();

        $searchCategory = $this->categoryRepository->find($id);


        if($searchCategory === null){
            throw  new NotFoundHttpException("Category is not found.");
        }

        if(($user->getCompany()->getId() === $searchCategory->getCompanyId())){
            return $searchCategory;
        }

        if($user->getCompany()->getId() !== $searchCategory->getCompanyId()){

            throw new HttpException(Response::HTTP_FORBIDDEN,"This category is not in your company.");
        }

        return null;
    }


}