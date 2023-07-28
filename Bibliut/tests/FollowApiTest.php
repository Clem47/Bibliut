<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FollowApiTest extends WebTestCase
{
    public function testFollowList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/test/listFollows');
        $followers = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful("La liste des followers est bien obtenue");
        $this->assertGreaterThanOrEqual(
            50,
            count($followers),
            "Le nombre de followers doit correspondre aux données de test"
        );
    }
}
