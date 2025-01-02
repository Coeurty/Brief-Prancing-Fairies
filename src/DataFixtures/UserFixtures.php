<?php

namespace App\DataFixtures;

use App\Entity\User;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $faker;
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUser($manager, 'admin@oo.fr', '123', ['ROLE_ADMIN'], 'admin');

        for ($i = 0; $i < 3; $i++) {
            $moderator = $this->createUser($manager, $this->faker->email(), '123', ['ROLE_MODERATOR'], $this->faker->userName());
            $this->addReference('moderator_'.$i, $moderator);
        }

        for ($i = 0; $i < 10; $i++) {
            $user = $this->createUser($manager, $this->faker->email(), '123', ['ROLE_USER'], $this->faker->userName());
            $this->addReference('user_'.$i, $user);
        }

        $manager->flush();
    }

    private function createUser(ObjectManager $manager, string $email, string $plainPassword, array $roles, string $nickname): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $password = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $user->setNickname($nickname);
        $manager->persist($user);

        return $user;
    }
}