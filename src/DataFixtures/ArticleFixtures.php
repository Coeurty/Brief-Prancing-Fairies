<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\DataFixtures\UserFixtures;
use Faker\Factory as FakerFactory;
use App\DataFixtures\Traits\DateTrait;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\ArticleCategoryFixtures;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    use DateTrait;

    private $faker;
    private $slugger;

    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->slugger = new AsciiSlugger();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setTitle($this->faker->sentence(6));
            $article->setSlug($this->slugger->slug(strtolower($article->getTitle())));
            $article->setStandFirst($this->faker->text(100));
            $article->setCoverImage('coverImage.jpg');
            $article->setContent($this->faker->text(2000));
            $article->setCategory($this->getReference('article_category_' . $this->faker->numberBetween(0, 4), ArticleCategory::class));
            $article->setUser($this->getReference('moderator_' . $this->faker->numberBetween(0, 2), User::class));
            $article->setCreatedAt($this->createRandomDate());
            $article->setUpdatedAt($this->createRandomDate($article->getCreatedAt()));
            $manager->persist($article);

            $this->addReference('article_' . $i, $article);
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
