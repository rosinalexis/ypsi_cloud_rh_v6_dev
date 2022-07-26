<?php

namespace App\DataFixtures;

use App\Entity\Enums\Gender;
use App\Entity\Profile;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;


class ProfileFixtures extends Fixture
{
    private Generator $faker;
    const NUMBER_OF_FAKE_ELEMENT = 5;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i <self::NUMBER_OF_FAKE_ELEMENT; $i++){
            $profile = new Profile();
            $profile->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
                ->setGender($this->faker->randomElement([Gender::Autre->value,Gender::Madame->value,Gender::Monsieur->value,Gender::Mademoiselle->value]))
                ->setAddress($this->faker->address())
                ->setPhone($this->faker->phoneNumber())
                ->setBirthdate(new DateTimeImmutable($this->faker->date() ) ?? new DateTimeImmutable())
                ->setUser( $this->getReference(UserFixtures::USER_REFERENCE.$i));

            $manager->persist($profile);
            $manager->flush();
        }
    }
}
