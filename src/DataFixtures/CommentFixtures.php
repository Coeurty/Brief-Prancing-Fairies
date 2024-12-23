<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
        {
            $this->faker = FakerFactory::create('fr_FR');
        }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $articles = $manager->getRepository(Article::class)->findAll();

        if (empty($articles)) {
            throw new \Exception('No articles found in the database.');
        }


        for ($i = 0; $i < 100; $i++) {
            $comment = new Comment();
            $comment->setContent($this->faker->text(200));
            $comment->setIp($this->faker->ipv4);
            $comment->setArticle($this->faker->randomElement($articles));
            $comment->setUser($this->faker->randomElement($users));
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
