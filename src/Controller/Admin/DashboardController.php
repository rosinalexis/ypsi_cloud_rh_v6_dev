<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Job;
use App\Entity\JobAd;
use App\Entity\Profile;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[isGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return  $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('YPSI CLOUD RH API V6')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Administration');
        yield MenuItem::subMenu('Categories','fa fa-border-style')
            ->setSubItems([
            MenuItem::linkToCrud('Create Category','fas fa-plus',Category::class)
                ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Show Categories', 'fas fa-eye', Category::class)
        ]);

        yield MenuItem::subMenu('Companies','fa fa-building')
            ->setSubItems([
                MenuItem::linkToCrud('Create Company','fas fa-plus',Company::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Show Companies', 'fas fa-eye', Company::class)
            ]);

        yield MenuItem::subMenu('Job ads','fa fa-newspaper')
            ->setSubItems([
                MenuItem::linkToCrud('Create Job AD','fas fa-plus',JobAd::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Show Job ads', 'fas fa-eye', JobAd::class)
            ]);

        yield MenuItem::subMenu('Contacts','fa fa-people-arrows')
            ->setSubItems([
                MenuItem::linkToCrud('Create Contact','fas fa-plus',Contact::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Show Contacts', 'fas fa-eye', Contact::class)
            ]);

        yield MenuItem::subMenu('Jobs','fa fa-briefcase')
            ->setSubItems([
                MenuItem::linkToCrud('Create Job','fas fa-plus',Job::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Show Jobs', 'fas fa-eye', Job::class)
            ]);

        yield MenuItem::subMenu('Profiles','fa fa-id-card')
            ->setSubItems([
                MenuItem::linkToCrud('Create Profile','fas fa-plus',Profile::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Show Profiles', 'fas fa-eye', Profile::class)
            ]);

        yield MenuItem::subMenu('Users','fa fa-users')
            ->setSubItems([
                MenuItem::linkToCrud('Create User','fas fa-plus',User::class)
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Show Users', 'fas fa-eye', User::class)
            ]);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX,Action::DETAIL);
    }


}
