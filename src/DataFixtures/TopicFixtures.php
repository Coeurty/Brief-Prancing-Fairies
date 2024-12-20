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
    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->slugger = new AsciiSlugger();
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $TopicCategories = $manager->getRepository(TopicCategory::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $topic = new Topic();
            $topic->setTitle($this->faker->sentence(6));
            $topic->setSlug($this->slugger->slug(strtolower($topic->getTitle())));
            $topic->setStrandFirst($this->faker->sentence(12));
            $topic->setUser($this->faker->randomElement($users));
            $topic->setCategory($this->faker->randomElement($TopicCategories));
            $manager->persist($topic);
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
