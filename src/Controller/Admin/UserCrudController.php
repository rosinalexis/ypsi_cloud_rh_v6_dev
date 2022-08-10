<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInPlural('Users')
            ->setEntityLabelInSingular('User');
        return parent::configureCrud($crud);
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER'];
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email')->setFormTypeOption('disabled',$pageName != Crud::PAGE_NEW),
            AssociationField::new('company'),
            ChoiceField::new('roles')
                ->renderAsBadges()
                ->setChoices(array_combine($roles, $roles))
                ->allowMultipleChoices()
                ->renderExpanded(),
            BooleanField::new('blocked')->renderAsSwitch(false)->hideOnForm(),
            BooleanField::new('confirmed')->renderAsSwitch(false)->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if(!$entityInstance instanceof User) return;

        $entityInstance->setPassword('$2y$13$HKBAZpWtSy1paUCxAE.zDesTYL6NGFJ6FBIZ3tSN3XTWWTais7nYy')
            ->setConfirmed(true)
            ->setBlocked(false);

        parent::persistEntity($entityManager, $entityInstance);
    }

}
