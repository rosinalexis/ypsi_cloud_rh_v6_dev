<?php

namespace App\DataFixtures;


use App\Entity\Category;
use App\Entity\Job;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class JobFixtures extends Fixture
{
    private Generator $faker;
    const NUMBER_OF_FAKE_ELEMENT = 15;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
       for($i =1; $i < self::NUMBER_OF_FAKE_ELEMENT;$i++)
       {
           $job = new Job();
           $job->setTitle($this->faker->jobTitle());
           $job->setDescription($this->faker->realText());

           /**
            * @var $user User
            */
           $user =  $this->getReference(UserFixtures::USER_REFERENCE.$i);

           $job->setUser($user);

           while (!$job->getCategory())
           {
               /**
                * @var $category Category
                */
               $category = $this->getReference(CategoryFixtures::CATEGORY_REFERENCE.$this->faker->numberBetween(1,14));

               if ($category->getCompanyId() == $job->getUser()->getCompany()->getId())
               {
                   $job->setCategory($category);
               }
           }

           $manager->persist($job);
           $manager->flush();
       }

    }
}
