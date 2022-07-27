<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class CompanyFixtures extends Fixture
{
    private Generator $faker;
    const NUMBER_OF_FAKE_ELEMENT = 4;
    public const COMPANY_REFERENCE ='company_';

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i =1; $i <self::NUMBER_OF_FAKE_ELEMENT;$i++){

            $company = new Company();
            $company->setName($this->faker->company())
                ->setEmail($this->faker->companyEmail())
                ->setAddress($this->faker->address())
                ->setRegion($this->faker->region())
                ->setPhone($this->faker->serviceNumber())
                ->setRegion($this->faker->region())
                ->setSiret($this->faker->siret());

            foreach ( $this->faker->department() as $code => $name){
                $company->setDepartmentName($name);
                $company->setDepartmentNumber(intval($code));
            }

            //ajout des utilisateurs
            if ($i  == 1){
                for($j=1;$j<5;$j++){
                    /**
                     * @var $user User
                     */
                    $user = $this->getReference(UserFixtures::USER_REFERENCE.$j);

                    /**
                     * @var $userAdmin User
                     */
                    $userAdmin =$this->getReference(UserFixtures::USER_ADMIN_REFERENCE);

                    /**
                     * @var $userTest User
                     */
                    $userTest =$this->getReference(UserFixtures::USER_TEST_REFERENCE);

                    $company->addUser($user);
                    $company->addUser($userAdmin);
                    $company->addUser($userTest);
                }
            }

            if ($i == 2){
                for($j=5;$j<10;$j++){

                    /**
                     * @var $user User
                     */
                    $user =$this->getReference(UserFixtures::USER_REFERENCE.$j);

                    $company->addUser($user);
                }
            }

            if ($i == 3){
                for($j=10;$j<15;$j++){
                    /**
                     * @var $user User
                     */
                    $user =$this->getReference(UserFixtures::USER_REFERENCE.$j);
                    $company->addUser($user);

                }
            }

            //ajout des paramètres de l'application
            $company  =$this->addDefaultSettingsToCompany($company);

            $manager->persist($company);
            $manager->flush();

            $this->addReference(self::COMPANY_REFERENCE.$i,$company);
        }
    }


    private function addDefaultSettingsToCompany(Company $company): Company
    {
        $default_settings = [
            "email_template_list" => [
                [
                    "title" => "template accusé de réception",
                    "enabled" => false,
                    "subject" => " accusé de réception test",
                    "content" => [
                        "ops" => []
                    ],
                    "html_content" => "<p>Bonjour  %user%, </p> <br/> <p>je suis la version 1</p> <br/> <p>cordialement</p>"
                ],
                [
                    "title" => "template de date",
                    "enabled" => false,
                    "subject" => "template accusé de réception date",
                    "content" => [
                        "ops" => []
                    ],
                    "html_content" => "<p>Bonjour  %user%, </p> <br/> <p>je suis la version 2</p> <br/> <p>cordialement</p>"
                ]
            ],
            "equipment_config_list" => [
                [
                    "title" => "Informatique",
                    "details" => ["ordinateur", "souris", "tableau"]
                ],
                [
                    "title" => "Marketing",
                    "details" => ["ordinateur mac", "souris", "tableau"]
                ]
            ],

            "document_config_list" => [
                [
                    "title" => "Droit à l'image",
                    "section" => "Droit à l'image",
                    "content" => "Contenu  de l'application"
                ]
            ],
            "business_support_list" => [
                [
                    "title" => "test aide 1",
                    "assistance_type" => "test aide regionnale",
                    "description" => "test de description"
                ]
            ],
        ];

        $company->setSettings($default_settings);

        return $company;
    }
}
