<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserApiTest extends WebTestCase
{
    public function testUserList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/test/listUsers');
        $users = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful("La liste des utilisateurs est bien obtenue");
        $this->assertGreaterThanOrEqual(
            100,
            count($users),
            "Le nombre d'utilisateurs doit correspondre aux données de test"
        );

        $usernames = [];
        foreach ($users as $user) {
            $this->assertArrayHasKey('id', $user, "Un utilisateur a un id");
            $this->assertArrayHasKey('username', $user, "Un utilisateur a un nom d'utilisateur");

            $usernames[] = $user['username'];
        }

        $this->assertContains('admin', $usernames, "Test de la présence d'un nom d'utilisateur connu");
    }

    public function testAddUser()
    {
        $client = static::createClient();

        $username = 'bob.bob';
        $password = 'bob';
        $first_name = 'bob';
        $last_name = 'bob';

        $client->request('POST', '/test/addUser', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $username,
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $user = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals('bob.bob', $user['username'], "Le username de l'utilisateur doit être 'bob.bob'");
        $this->assertEquals('bob', $user['password'], "Le mot de passe de l'utilisateur doit être 'bob'");
        $this->assertEquals('bob', $user['first_name'], "Le prénom de l'utilisateur doit être 'bob'");
        $this->assertEquals('bob', $user['last_name'], "Le nom de l'utilisateur doit être 'bob'");
        $this->assertEquals(0, count($user['loans']), "L'utilisateur n'a aucun emprunt");
        $this->assertEquals(0, count($user['follows']), "L'utilisateur ne suit personne");
        $this->assertEquals(0, count($user['followers']), "L'utilisateur n'est pas suivit");
    }
}
