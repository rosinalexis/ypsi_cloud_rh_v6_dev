<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Security\Core\Security;

final class CategoryDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(Security $security,EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->security =$security;
    }

    public function supports($data, array $context = []): bool
    {
       return $data instanceof Category;
    }


    public function persist($data, array $context = [])
    {
        if ($data instanceof Category && (($context['collection_operation_name'] ?? null) === 'post'))
        {
            $this->addCompanyIdToCategory($data);
            $this->em->persist($data);
        }


        $this->em->flush();
    }

    public function remove($data, array $context = [])
    {
       $this->em->remove($data);
       $this->em->flush();
    }

    private function addCompanyIdToCategory(Category $category){
        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        if($user->getCompany())
        {
            $companyId = $user->getCompany()->getId();
            $category->setCompanyId($companyId);
        }else {
            throw new ConflictHttpException("This account has no company. Please create a first.");
        }

    }
}