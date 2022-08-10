<?php

namespace App\Controller\Admin;

use App\Entity\JobAd;
use App\Service\ReferenceGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JobAdCrudController extends AbstractCrudController
{
    private ReferenceGeneratorService $referenceGeneratorService;

    public function __construct(
        ReferenceGeneratorService $referenceGeneratorService
    )
    {
        $this->referenceGeneratorService =$referenceGeneratorService;
    }

    public static function getEntityFqcn(): string
    {
        return JobAd::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            BooleanField::new('published'),
            TextField::new('reference')->hideOnForm(),
            TextField::new('title'),
            TextField::new('region'),
            TextField::new('contractType'),
            TextField::new('wage'),
            AssociationField::new('category'),
            AssociationField::new('contacts'),
            IntegerField::new('companyId'),
            TextareaField::new('description')->hideOnIndex(),
            ArrayField::new('requirements')->hideOnIndex(),
            ArrayField::new('tasks')->hideOnIndex(),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(!$entityInstance instanceof JobAd) return;

        $entityInstance->setReference($this->referenceGeneratorService->getRandomSecureReference());

        parent::persistEntity($entityManager, $entityInstance);
    }

}
