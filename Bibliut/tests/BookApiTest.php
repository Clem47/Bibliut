<?php

namespace App\Tests;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookApiTest extends WebTestCase
{
    public function testBooksList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/test/listBooks');
        $books = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful("La liste des livres est bien obtenue");
        $this->assertGreaterThanOrEqual(
            200,
            count($books),
            "Le nombre de livres doit correspondre aux données de test"
        );

        $titles = [];
        foreach ($books as $book) {
            $this->assertArrayHasKey('id', $book, "Un livre a un id");
            $this->assertArrayHasKey('title', $book, "Un livre a un titre");

            $titles[] = $book['title'];
        }
    }

    public function testAddBook()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $title = 'Shining';
        $releaseDate = '1977-01-28T00:00:00.000000Z';
        $acquisitionDate = '2023-03-29T00:00:00.000000Z';

        $client->request('POST', '/test/addBook', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => $title,
            'release_date' => $releaseDate,
            'acquisition_date' => $acquisitionDate,
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $book = $entityManager->getRepository(Book::class)->findOneBy(['title' => $title]);

        $this->assertNotNull($book);
        $this->assertEquals($title, $book->getTitle(), "Le titre du livre doit être 'Shining'");
        $this->assertEquals($releaseDate, $book->getReleaseDate()->format('Y-m-d\TH:i:s.u\Z'));
        $this->assertEquals($acquisitionDate, $book->getAcquisitionDate()->format('Y-m-d\TH:i:s.u\Z'));
        $this->assertEquals(0, count($book->getAuthors()), "Aucun auteur pour le livre");
    }

    public function testAddBookToAuthor()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $first_name = 'Victor';
        $last_name = 'Hugo';

        $client->request('POST', '/test/addAuthor', [], [], [], json_encode([    
            'first_name' => $first_name,    
            'last_name' => $last_name
            ]));

        $title = 'Les Misérables';

        $client->request('POST', '/test/addBook', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => $title,
            'release_date' => '1862-04-03T00:00:00.000000Z',
            'acquisition_date' => '2023-02-17T00:00:00.000000Z',
        ]));

        $book = $entityManager->getRepository(Book::class)->findOneBy(['title' => $title]);
        $author = $entityManager
            ->getRepository(Author::class)
            ->findOneBy(['first_name' => $first_name, 'last_name' => $last_name]);

        $client->request('POST', '/test/addBookToAuthor', [], [], [], json_encode([    
            'id_author' => $author->getId(),    
            'id_book' => $book->getId()
            ]));

        $this->assertEquals(1, count($book->getAuthors()), "Le livre a un auteur");
        $this->assertEquals(1, count($author->getBooks()), "L'auteur a un livre");

        /*
        $title2 = 'Notre-Dame de Paris';

        $client->request('POST', '/test/addBook', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'title' => $title2,
            'release_date' => '1831-03-16T00:00:00.000000Z',
            'acquisition_date' => '2023-03-03T00:00:00.000000Z',
        ]));

        $book2 = $entityManager->getRepository(Book::class)->findOneBy(['title' => $title2]);
        $author2 = $entityManager
            ->getRepository(Author::class)
            ->findOneBy(['first_name' => $first_name, 'last_name' => $last_name]);

        $this->assertEquals($author->getId(), $author2->getId());
        $this->assertEquals('Victor', $author2->getFirstName());
        $this->assertEquals('Hugo', $author2->getLastName());

        $this->assertEquals('Notre-Dame de Paris', $book2->getTitle());

        $client->request('POST', '/test/addBookToAuthor', [], [], [], json_encode([    
            'id_author' => $author2->getId(),    
            'id_book' => $book2->getId()
            ])
        );

        $this->assertEquals(1, count($book2->getAuthors()), "Le livre a un auteur");
        dump($author2->getBooks());
        $this->assertEquals(2, count($author2->getBooks()), "L'auteur a deux livres");
        */
    }
}
