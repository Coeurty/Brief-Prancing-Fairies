<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Topic;
use App\Entity\TopicCategory;
use App\DataFixtures\UserFixtures;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\TopicCategoryFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class TopicFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;
    private $slugger;

    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->slugger = new AsciiSlugger();
    }

    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 10; $i++) {
            $topic = new Topic();
            $topic->setTitle($this->faker->sentence(6));
            $topic->setSlug($this->slugger->slug(strtolower($topic->getTitle())));
            $topic->setStrandFirst($this->faker->sentence(12));
            $topic->setUser($this->getReference('user_' . $this->faker->numberBetween(0, 9), User::class));
            $topic->setCategory($this->getReference('topic_category_' . $this->faker->numberBetween(0, 4), TopicCategory::class));
            $manager->persist($topic);

            $this->addReference('topic_' . $i, $topic);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TopicCategoryFixtures::class,
        ];
    }
}
