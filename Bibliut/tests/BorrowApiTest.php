<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BorrowApiTest extends WebTestCase
{
    public function testBorrowList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/test/listBorrows');
        $books = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful("La liste des emprunts est bien obtenue");
        $this->assertGreaterThanOrEqual(
            200,
            count($books),
            "Le nombre d'emprunts doit correspondre aux données de test"
        );
    }
}
