<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\DataFixtures\UserFixtures;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\ArticleCategoryFixtures;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->slugger = new AsciiSlugger();
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $categories = $manager->getRepository(ArticleCategory::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setTitle($this->faker->sentence(6));
            $article->setSlug($this->slugger->slug(strtolower($article->getTitle())));
            $article->setStandFirst($this->faker->text(100));
            $article->setCoverImage('https://picsum.photos/1200/300');
            $article->setContent($this->faker->text(2000));
            $article->setCategory($this->faker->randomElement($categories));
            $article->setUser($this->faker->randomElement($users));
            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ArticleCategoryFixtures::class,
        ];
    }
}
