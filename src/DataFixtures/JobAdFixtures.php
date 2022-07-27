<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\JobAd;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class JobAdFixtures extends Fixture
{
    private Generator $faker;
    public const NUMBER_OF_FAKE_ELEMENT = 10;
    //public const JOB_AD_REFERENCE = 'job_ad_';

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < self::NUMBER_OF_FAKE_ELEMENT; $i++) {
            $jobAd = new JobAd();
            $jobAd->setTitle($this->faker->jobTitle())
                ->setDescription($this->faker->realText())
                ->setReference($this->faker->uuid())
                ->setRegion($this->faker->region())
                ->setContractType($this->faker->randomElement(['CDD', 'CDI', 'ITERIM']))
                ->setPublished($this->faker->boolean())
                ->setRequirements(["requirement1", "requirement2", "requirement3"])
                ->setTasks(["task1", "task2", "task3"])
                ->setWage($this->faker->numberBetween(1000, 3000) . "€/ mois");

            if ($jobAd->isPublished()) {
                $jobAd->setPublishedAt(new \DateTimeImmutable());
            }

            /**
             * @var $category Category
             */
            $category = $this->getReference(CategoryFixtures::CATEGORY_REFERENCE . $this->faker->numberBetween(1,14));

            $jobAd->setCategory($category);
            $jobAd->setCompanyId($category->getCompanyId());

            $manager->persist($jobAd);
            $manager->flush();
        }

    }
}
