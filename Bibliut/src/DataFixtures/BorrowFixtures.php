<?php

namespace App\DataFixtures;

use App\Entity\Borrow;
use App\Entity\User;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @DependsOn({"App\DataFixtures\UserFixtures.php"})
 */
class BorrowFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();
        $books = $manager->getRepository(Book::class)->findAll();
        for ($i = 0; $i < 200; ++$i) {
            $borrow = new Borrow();
            $borrow->setUser($faker->randomElement($users));
            $borrow->setBook($faker->randomElement($books));
            $borrow->setDateBorrow($faker->dateTimeBetween('-2 year', 'now'));
            $borrow->setDateReturn($faker->dateTimeBetween($borrow->getDateBorrow(), '+1 year'));
            $manager->persist($borrow);
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
