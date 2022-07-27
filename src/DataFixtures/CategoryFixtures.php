<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class CategoryFixtures extends Fixture
{
    private Generator $faker;
    public const NUMBER_OF_FAKE_ELEMENT=15;
    public const CATEGORY_REFERENCE = 'cateogry_';

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $listCategory = [
            "Informatique",
            "Marketing",
            "Secrétariat",
            "Restaurant",
            "Management"
        ];

       for($i=1;$i<self::NUMBER_OF_FAKE_ELEMENT;$i++) {

            $category = new Category;
            $category->setTitle($this->faker->randomElement($listCategory));
            $category->setDescription($this->faker->realText());
            $category->setCompanyId(
                $this->getReference(
                    CompanyFixtures::COMPANY_REFERENCE.$this->faker->numberBetween(1,3)
                )->getId());
            $manager->persist($category);
            $manager->flush();

            $this->setReference(self::CATEGORY_REFERENCE . $i, $category);
        }
    }
}
