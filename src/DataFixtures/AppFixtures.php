<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

//use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

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
            ProfileFixtures::class
        ];
    }
}
