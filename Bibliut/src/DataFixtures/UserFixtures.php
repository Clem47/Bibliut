<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use GuzzleHttp\Client;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $client = new Client();
        for ($i = 0; $i < 50; ++$i) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setProfilePicture(
                $client->get(
                    'https://randomuser.me/api/portraits/men/' . strval($i) . '.jpg'
                )->getBody()
            );

            $manager->persist($user);
        }
        for ($i = 0; $i < 50; ++$i) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setProfilePicture(
                $client->get(
                    'https://randomuser.me/api/portraits/women/' . strval($i) . '.jpg'
                )->getBody()
            );

            $manager->persist($user);
        }
        if (!$manager->getRepository(User::class)->findOneBy(['username' => 'admin'])) {
            $user = new User();
            $user->setUsername('admin');
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setFirstName('Admin');
            $user->setLastName('Admin');
            $user->setProfilePicture(file_get_contents($faker->imageUrl(200, 200, 'people')));
            $manager->persist($user);
            $manager->flush();
        }
    }
}
