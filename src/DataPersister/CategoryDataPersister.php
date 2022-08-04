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

    /**
     * @param $data Category
     * @param array $context
     */
    public function remove($data, array $context = [])
    {
        // si la catégorie n'est pas vide
        if($data->getJobAds() && $data->getJobs()){
            throw  new ConflictHttpException("This Category is used in some Job and JobAds.You should remove them first.");
        }

        // si où la catégorie est vide
       $this->em->remove($data);
       $this->em->flush();
    }

    private function addCompanyIdToCategory(Category $category){
        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        // il faut obligatoirement avoir une entreprise
        if(!$user->getCompany())
        {
            throw new ConflictHttpException("This account has no company. Please create one first.");
        }

        $companyId = $user->getCompany()->getId();
        $category->setCompanyId($companyId);
    }
}