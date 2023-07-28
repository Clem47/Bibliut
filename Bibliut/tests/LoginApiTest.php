<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginApiTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();

        $username = 'admin';
        $password = 'password';

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => $username,
            'password' => $password,
        ]));

        $response = $client->getResponse();
        $this->assertNotNull($response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $responseData);
    }
}
