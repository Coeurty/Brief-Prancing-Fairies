<?php

namespace App\DataFixtures;

use App\Entity\TopicCategory;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TopicCategoryFixtures extends Fixture
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
        for ($i = 0; $i < 5; $i++) {
            $category = new TopicCategory();
            $category->setName($this->faker->sentence(3));
            $category->setSlug($this->slugger->slug(strtolower($category->getName())));
            $manager->persist($category);

            $this->addReference('topic_category_'.$i, $category);
        }
        $manager->flush();
    }
}
