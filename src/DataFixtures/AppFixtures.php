<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

//use Symfony\Component\Security\Csrf\TokenGeneratorService\TokenGeneratorInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        //
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CompanyFixtures::class,
            CategoryFixtures::class,
            JobFixtures::class,
            ProfileFixtures::class,
            JobAdFixtures::class
        ];
    }
}
