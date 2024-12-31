<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Message;
use App\DataFixtures\UserFixtures;
use Faker\Factory as FakerFactory;
use App\DataFixtures\TopicFixtures;
use App\DataFixtures\Traits\DateTrait;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;




class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    use DateTrait;

    private $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; $i++) {
            $message = new Message();
            $message->setContent($this->faker->sentence(10));
            $message->setIp($this->faker->ipv4);
            $message->setUser($this->getReference('user_' . $this->faker->numberBetween(0, 9), User::class));
            $message->setTopic(($this->getReference('topic_' . $this->faker->numberBetween(0, 9), Topic::class)));
            $message->setCreatedAt($this->createRandomDate());
            $message->setUpdatedAt($this->createRandomDate($message->getCreatedAt()));

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
