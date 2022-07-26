<?php

namespace App\DataFixtures;

use App\Entity\Enums\Gender;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

//use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private Generator $faker;
    const NUMBER_OF_FAKE_ELEMENT = 5;

    public function __construct(UserPasswordHasherInterface $passwordHasher,)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {
        for ($i = 0; $i < $this::NUMBER_OF_FAKE_ELEMENT; $i++) {
            $user = new User();
            $user
                ->setEmail($this->faker->email())
                ->setRoles(USER::ROLE_USER)
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
                ->setBlocked($this->faker->randomElement([true, false]))
                ->setConfirmed(true);

            $manager->persist($user);
            $manager->flush();
        }

        //ajout d'un admin

        $user = new User();
        $user
            ->setEmail('admin@admin.fr')
            ->setRoles(USER::ROLE_ADMIN)
            ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
            ->setProfile( $this->getReference("profile_$i"))
            ->setBlocked(false)
            ->setConfirmed(true);

        $manager->persist($user);
        $manager->flush();

        //ajout d'un utilisateur

        $user = new User();
        $user
            ->setEmail('testman@test.fr')
            ->setRoles(USER::ROLE_ADMIN)
            ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
            ->setBlocked(false)
            ->setConfirmed(true);

        $manager->persist($user);
        $manager->flush();

    }

    public function loadProfiles(ObjectManager $manager)
    {
        for ($i=0; $i <self::NUMBER_OF_FAKE_ELEMENT; $i++){
            $profile = new Profile();
            $profile->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
                ->setGender($this->faker->randomElement([Gender::Autre,Gender::Madame,Gender::Monsieur,Gender::Mademoiselle]))
                ->setAddress($this->faker->address())
                ->setPhone($this->faker->phoneNumber())
                ->setBirthdate(strtotime($this->faker->date()) ?? new \DateTimeImmutable());

            $manager->persist($profile);
            $manager->flush();

            $this->setReference("profile_$i", $profile);
        }
    }



}
