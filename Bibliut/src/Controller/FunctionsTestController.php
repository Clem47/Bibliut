<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\BorrowRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/test', name: 'app_test')]
class FunctionsTestController extends AbstractController
{
    #[Route('/listAuthors', name: 'app_test_listAuthors')]
    public function listAuthors(AuthorRepository $authorRepository): Response
    {
        $authors = $authorRepository->createQueryBuilder('a')
            ->select('a.id', 'a.first_name', 'a.last_name')
            ->getQuery()
            ->getArrayResult();

        return $this->json($authors);
    }

    #[Route('/listBooks', name: 'app_test_listBooks')]
    public function listBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->createQueryBuilder('b')
            ->select('b.id', 'b.title')
            ->getQuery()
            ->getArrayResult();

        return $this->json($books);
    }

    #[Route('/listUsers', name: 'app_test_listUsers')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $books = $userRepository->createQueryBuilder('u')
            ->select('u.id', 'u.username')
            ->getQuery()
            ->getArrayResult();

        return $this->json($books);
    }

    #[Route('/listBorrows', name: 'app_test_listBorrows')]
    public function listBorrows(BorrowRepository $borrowRepository): Response
    {
        $borrows = $borrowRepository->createQueryBuilder('b')
            ->select('b.id')
            ->getQuery()
            ->getArrayResult();

        return $this->json($borrows);
    }

    #[Route('/listFollows', name: 'app_test_listFollows')]
    public function listFollows(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $follows = [];

        foreach ($users as $user) {
            $followers = $user->getFollowers();
            foreach ($followers as $follower) {
                $follows[] = [
                    'follower' => $follower->getUsername(),
                    'followed' => $user->getUsername(),
                ];
            }
        }

        $response = new JsonResponse($follows);

        return $response;
    }

    #[Route('/addAuthor', name: 'app_test_addAuthor', methods: ["POST"])]
    public function addAuthor(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['first_name']) || !isset($data['last_name'])) {
            return new JsonResponse(['error' => 'Les champs first_name et last_name sont obligatoires.'], 400);
        }

        $author = new Author();
        $author->setFirstName($data['first_name']);
        $author->setLastName($data['last_name']);

        $entityManager->persist($author);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $author->getId(),
            'first_name' => $author->getFirstName(),
            'last_name' => $author->getLastName(),
            'books' => $author->getBooks()
        ], 200);
    }

    #[Route('/addBook', name: 'app_test_addBook', methods: ["POST"])]
    public function addBook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        if (!isset($data['title']) || !isset($data['release_date']) || !isset($data['acquisition_date'])) {
            return new JsonResponse(
                ['error' => 'Les champs title, release_date et acquisition_date sont obligatoires.'],
                400
            );
        }

        $book = new Book();
        $book->setTitle($data['title']);
        $releaseDate = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['release_date']);
        $book->setReleaseDate($releaseDate);
        $acquisition_date = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $data['acquisition_date']);
        $book->setAcquisitionDate($acquisition_date);

        $entityManager->persist($book);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'release_date' => $book->getReleaseDate(),
            'acquisition_date' => $book->getAcquisitionDate(),
            'authors' => $book->getAuthors()
        ], 200);
    }

    #[Route('/addUser', name: 'app_test_addUser', methods: ["POST"])]
    public function addUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            !isset($data['username']) || !isset($data['password']) ||
            !isset($data['first_name']) || !isset($data['last_name'])
        ) {
            return new JsonResponse(
                ['error' => 'Les champs username, password, first_name et last_name sont obligatoires.'],
                400
            );
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setPassword($data['password']);
        $user->setFirstName($data['first_name']);
        $user->setLastName($data['last_name']);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'loans' => $user->getLoans(),
            'follows' => $user->getFollows(),
            'followers' => $user->getFollowers()
        ], 200);
    }

    #[Route('/addBookToAuthor', name: 'app_test_addBookToAuthor', methods: ["POST"])]
    public function addBookToAuthor(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['id_author']) || !isset($data['id_book'])) {
            return new JsonResponse(
                ['error' => 'Les champs id_author et id_book sont obligatoires.'],
                400
            );
        }

        $author = $entityManager->getRepository(Author::class)->find($data['id_author']);
        $book = $entityManager->getRepository(Book::class)->find($data['id_book']);
        $author->addBook($book);

        $entityManager->persist($book);
        $entityManager->persist($author);
        $entityManager->flush();

        return new JsonResponse([
            'authors' => $book->getAuthors(),
            'books' => $author->getBooks(),
        ], 200);
    }
}
