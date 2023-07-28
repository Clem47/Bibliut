<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorApiTest extends WebTestCase
{
    public function testAuthorList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/test/listAuthors');
        $authors = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful("La liste des auteurs est bien obtenue");
        $this->assertGreaterThanOrEqual(
            50,
            count($authors),
            "Le nombre d'auteurs doit correspondre aux données de test"
        );

        $first_names = [];
        $last_names = [];
        foreach ($authors as $author) {
            $this->assertArrayHasKey('id', $author, "Un auteur a un id");
            $this->assertArrayHasKey('first_name', $author, "Un auteur a un prénom");
            $this->assertArrayHasKey('last_name', $author, "Un auteur a un nom de famille");

            $first_names[] = $author['first_name'];
            $last_names[] = $author['last_name'];
        }
    }

    public function testAddAuthor(): void
    {
        $client = static::createClient();
        $client->request('POST', '/test/addAuthor', [], [], [], json_encode([
            'first_name' => 'Stephen',    
            'last_name' => 'King'
            ]));

        $author = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('Stephen', $author['first_name'], "Le prénom de l'auteur doit être 'Stephen'");
        $this->assertEquals('King', $author['last_name'], "Le nom de l'auteur doit être 'King'");
        $this->assertEquals(0, count($author['books']), "L'auteur ne doit pas avoir de livre");
    }
}
