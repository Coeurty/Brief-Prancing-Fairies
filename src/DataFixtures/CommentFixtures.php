<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Comment;
use Faker\Factory as FakerFactory;
use App\DataFixtures\ArticleFixtures;
use App\DataFixtures\Traits\DateTrait;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    use DateTrait;

    private $faker;

    public function __construct()
        {
            $this->faker = FakerFactory::create('fr_FR');
        }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 100; $i++) {
            $comment = new Comment();
            $comment->setContent($this->faker->text(200));
            $comment->setIp($this->faker->ipv4);
            $comment->setArticle($this->getReference('article_' . $this->faker->numberBetween(0, 19), Article::class));
            $comment->setUser($this->getReference('user_' . $this->faker->numberBetween(0, 9), User::class));
            $comment->setCreatedAt($this->createRandomDate());
            $comment->setIsReported($this->faker->boolean(20));
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ArticleFixtures::class,
        ];
    }
}
