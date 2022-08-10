<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use App\Entity\Profile;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProfileCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Profile::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user'),
            ChoiceField::new('Gender')->setChoices([
                'Mr'=> 'M',
                'Mrs'=>'Mme',
                'Miss'=>'Mlle',
                'Other'=>'Autre'
            ])->autocomplete(),
            TextField::new('lastname'),
            TextField::new('firstname'),
            TextField::new('lastname'),
            TelephoneField::new('phone'),
            TextField::new('address'),
            DateField::new('birthdate'),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm()

        ];
    }

}
