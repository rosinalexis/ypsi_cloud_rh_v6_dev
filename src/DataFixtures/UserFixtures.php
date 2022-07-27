<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private Generator $faker;
    const NUMBER_OF_FAKE_ELEMENT = 15;
    public const USER_REFERENCE ='user_';
    public const USER_ADMIN_REFERENCE ='user_admin';
    public const USER_TEST_REFERENCE ='user_test';

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadSimpleUsers($manager);
        $this->loadAdminUser($manager);
        $this->loadTestUser($manager);
    }

    public function loadSimpleUsers(ObjectManager $manager)
    {
        for ($i = 1; $i < self::NUMBER_OF_FAKE_ELEMENT; $i++) {
            $user = new User();
            $user
                ->setEmail($this->faker->email())
                ->setRoles($this->faker->randomElement([User::ROLE_ADMIN,User::ROLE_USER]))
                ->setPassword($this->passwordHasher->hashPassword($user, 'e34g#52kNRtL'))
                ->setBlocked($this->faker->randomElement([true, false]))
                ->setConfirmed(true);

            $manager->persist($user);
            $manager->flush();

            $this->addReference(self::USER_REFERENCE.$i,$user);
        }
    }

    public function loadAdminUser(ObjectManager $manager)
    {
        //ajout d'un admin
        $user = new User();
        $user
            ->setEmail('admin@admin.fr')
            ->setRoles(USER::ROLE_ADMIN)
            ->setPassword($this->passwordHasher->hashPassword($user, 'e34g#52kNRtL'))
            ->setBlocked(false)
            ->setConfirmed(true);

        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::USER_ADMIN_REFERENCE,$user);
    }

    public function loadTestUser(ObjectManager $manager)
    {
        //ajout d'un utilisateur
        $user = new User();
        $user->setEmail('testman@test.fr')
            ->setRoles(USER::ROLE_ADMIN)
            ->setPassword($this->passwordHasher->hashPassword($user, 'e34g#52kNRtL'))
            ->setBlocked(false)
            ->setConfirmed(true);

        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::USER_TEST_REFERENCE,$user);
    }
}
