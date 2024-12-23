<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Message;
use App\DataFixtures\UserFixtures;
use Faker\Factory as FakerFactory;
use App\DataFixtures\TopicFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;




class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->slugger = new AsciiSlugger();
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $Topic = $manager->getRepository(Topic::class)->findAll();

        for ($i = 0; $i < 30; $i++) {
            $message = new Message();
            $message->setContent($this->faker->sentence(10));
            $message->setIp($this->faker->ipv4);
            $message->setUser($this->faker->randomElement($users));
            $message->setTopic($this->faker->randomElement($Topic));
            $manager->persist($message);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TopicFixtures::class,
        ];
    }
}
