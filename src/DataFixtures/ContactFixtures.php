<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Contact;
use Faker\Factory;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Traits\DateTrait;

class ContactFixtures extends Fixture
{
    use DateTrait;

    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        
        // Contact
        for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setNickname($this->faker->name())
                ->setEmail($this->faker->email())
                ->setSubject('Demande n°' . ($i + 1))
                ->setMessage($this->faker->text());

            $manager->persist($contact);
        }

        $manager->flush();
    }
}
