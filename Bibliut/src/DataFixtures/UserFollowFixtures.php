<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @DependsOn({"App\DataFixtures\UserFixtures.php"})
 */
class UserFollowFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();
        for ($i = 0; $i < 100; ++$i) {
            // Associe deux utilisateurs entre eux
            $user = $faker->randomElement($users);
            $userToFollow = $faker->randomElement($users);
            if ($user->getId() !== $userToFollow->getId()) {
                $user->addFollow($user, $userToFollow);
            }
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
