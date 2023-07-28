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
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Security\AccessTokenHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api', name: 'app_api')]
class ApiController extends AbstractController
{
    #[OA\Post(
        summary: "Login user"
    )]
    #[OA\Response(
        response: 200,
        description: "user token",
        content: new OA\JsonContent(
            example:'token : 1ec63c1099c9a5b3fe88745b694055375ddc2ec95a5b63506089cb3e23d76e29'
        )
    )]
    #[OA\Parameter(
        name: 'username',
        description: 'User username',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'password',
        description: 'User password',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[Route("/login", name: "_login", methods: ["POST"])]
    public function login(#[CurrentUser] ?User $user, AccessTokenHandler $accessTokenHandler): Response
    {
        if (null === $user) {
            return new JsonResponse(["error" => "User not found"], 400);
        }
        return new JsonResponse(["token" => $accessTokenHandler->createAccessToken($user)]);
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Post(
        summary: "Logout user"
    )]
    #[OA\Response(
        response: 200,
        description: "user token",
        content: new OA\JsonContent(
            example:'token : null'
        )
    )]
    #[IsGranted("ROLE_USER")]
    #[Security(name: "Bearer")]
    #[Route("/logout", name: "_logout", methods: ["POST"])]
    public function logout(#[CurrentUser] ?User $user, UserRepository $repository)
    {
        $user->setToken(null);
        $repository->save($user, true);
        return new JsonResponse(["token" => $user->getToken()]);
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Post(
        summary: "Enable user to follow other"
    )]
    #[OA\Response(
        response: 200,
        description: "Success message",
        content: new OA\JsonContent(
            example:'Success : <username> followed'
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error message",
        content: new OA\JsonContent(
            example:'Error : unknown username'
        )
    )]
    #[OA\Parameter(
        name: 'username',
        description: 'New friend username',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[IsGranted("ROLE_USER")]
    #[Security(name: "Bearer")]
    #[Route("/follow", name: "_follow", methods: ["POST"])]
    public function follow(#[CurrentUser] ?User $user, UserRepository $repository, Request $request): Response
    {
        $data = $request->request->all();
        $friend = $repository->findOneBy(["username" => $data["username"]]);
        if ($friend === null) {
            return new JsonResponse(["Error : unknown username"], 400);
        }
        $user->addFollow($user, $friend);
        $repository->save($user, true);
        return new JsonResponse(["Success : " => $friend->getUsername() . " followed"], 200);
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Get list of followed users by connected user"
    )]
    #[OA\Response(
        response: 200,
        description: "List of user",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: "#/components/schemas/UserInfos"
            )
        )
    )]
    #[IsGranted("ROLE_USER")]
    #[View(serializerGroups: ['basic'])]
    #[Security(name: "Bearer")]
    #[Route("/getFollow", name: "_getFollow", methods: ["GET"])]
    public function getFollow(#[CurrentUser] ?User $user)
    {
        return $user->getFollows();
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Get user profile picture"
    )]
    #[OA\Response(
        response: 200,
        description: "image",
    )]
    #[Route("/profile/{id}", name: "_profile", methods: ["GET"])]
    public function userProfilePicture($id, UserRepository $repository)
    {
        $user = $repository->findOneBy(["id" => $id]);
        return new Response(stream_get_contents($user->getProfilePicture()), 200, ['Content-Type' => 'image/png']);
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Post(
        summary: "Enable user to unfollow other"
    )]
    #[OA\Response(
        response: 200,
        description: "Success message",
        content: new OA\JsonContent(
            example:'Success : <username> unfollowed'
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Error message",
        content: new OA\JsonContent(
            example:'Error : unknown username'
        )
    )]
    #[OA\Parameter(
        name: 'username',
        description: 'Old friend username',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[IsGranted("ROLE_USER")]
    #[Security(name: "Bearer")]
    #[Route("/unfollow", name: "_unfollow", methods: ["POST"])]
    public function unfollow(#[CurrentUser] ?User $user, UserRepository $repository, Request $request): Response
    {
        $data = $request->request->all();
        $friend = $repository->findOneBy(["username" => $data["username"]]);
        if ($friend === null) {
            return new JsonResponse(["Error : unknown username"], 400);
        }
        $user->removeFollow($user, $friend);
        $repository->save($user, true);
        return new JsonResponse(["Success " => $friend->getUsername() . " unfollowed"], 200);
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Get book order by aquisition date (decrease) for one user"
    )]
    #[OA\Response(
        response: 200,
        description: "List of book",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: "#/components/schemas/BookInfos"
            )
        )
    )]
    #[OA\Parameter(
        name: 'quantity',
        in: 'query',
        description: 'Number of books',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'username',
        in: 'query',
        description: 'Name of user that we search books',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[View(serializerGroups: ['basic','basic_image'])]
    #[Route("/books", name: "_books", methods: ["GET"])]
    public function books(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $quantity = $request->query->get('quantity');
        $username = $request->query->get('username');

        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('b')
            ->from(Book::class, 'b')
            ->leftJoin('b.loans', 'l')
            ->orderBy('l.date_borrow', 'DESC')
            ->setMaxResults($quantity);

        if ($username !== null) {
            $user = $userRepository->findOneBy(['username' => $username]);

            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }

            $queryBuilder
                ->andWhere('l.user = :user')
                ->setParameter('user', $user);
        }

        $results = $queryBuilder->getQuery()->getResult();

        return [
            'books' => $results,
        ];
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Get information for one book"
    )]
    #[OA\Response(
        response: 200,
        description: "Book information",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: "#/components/schemas/BookInfos"
            )
        )
    )]
    #[View(serializerGroups: ['basic','basic_image'])]
    #[Route("/book/{id}", name: "_book", methods: ["GET"])]
    public function book(BookRepository $repo, $id)
    {
        $book = $repo->findOneBy(["id" => $id]);
        return $book;
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Get book order by aquisition date (decrease)"
    )]
    #[OA\Response(
        response: 200,
        description: "List of books",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: "#/components/schemas/BookInfos"
            )
        )
    )]
    #[OA\Parameter(
        name: 'quantity',
        in:'query',
        description: 'Number of books',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[View(serializerGroups: ['basic','basic_image'])]
    #[Route("/books/lastAcquisition", name: "_last-acquisition", methods: ["GET"])]
    public function last(Request $request, EntityManagerInterface $entityManager)
    {
        $quantity = $request->query->get("quantity");
        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('b')
            ->from(Book::class, 'b')
            ->orderBy('b.acquisition_date', 'DESC')
            ->setMaxResults($quantity);
        $results = $queryBuilder->getQuery()->getResult();
        return [
            'books' => $results,
        ];
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Get result of search by author or book title"
    )]
    #[OA\Response(
        response: 200,
        description: "List of books",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: "#/components/schemas/BookInfos"
            )
        )
    )]
    #[OA\Parameter(
        name: 'description',
        in: 'query',
        description: 'author name or book title',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'page id',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[View(serializerGroups: ['basic','basic_image'])]
    #[Route("/search", name: "_search", methods: ["GET"])]
    public function search(Request $request, EntityManagerInterface $entityManager)
    {
        $description = $request->query->get('description') ?? null;
        if ($description !== null) {
            $page = $request->query->get('page') ?? 1;
            $startIndex = $page == 0 ? 0 : 4 * $page - 4;
            $queryBuilder = $entityManager->createQueryBuilder()
                ->select('b')
                ->from(Book::class, 'b')
                ->innerJoin('b.authors', 'a')
                ->where("a.last_name like :description or a.first_name like :description or b.title like :description")
                ->setFirstResult($startIndex)
                ->setMaxResults("4")
                ->setParameter('description', $description . "%");
            $byAuthor = $queryBuilder->getQuery()->getResult();

            $queryBuilder = $entityManager->createQueryBuilder()
                ->select('COUNT(b)')
                ->from(Book::class, 'b')
                ->innerJoin('b.authors', 'a')
                ->where("a.last_name like :description or a.first_name like :description or b.title like :description")
                ->setParameter('description', $description . "%");
            $nbPage = $queryBuilder->getQuery()->getSingleScalarResult();

            return ["nbPage" => ceil($nbPage / 4),
                "books" => $byAuthor,
            ];
        }
        return new JsonResponse("Error : Description needed");
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Autocomplete for search"
    )]
    #[OA\Response(
        response: 200,
        description: "List of books",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: "#/components/schemas/BookInfos",
            )
        )
    )]
    #[OA\Parameter(
        name: 'description',
        in: 'query',
        description: 'author name or book title',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[View(serializerGroups: ['basic'])]
    #[Route("/autocomplete", name: "_autocomplete", methods: ["GET"])]
    public function autocomplete(Request $request, EntityManagerInterface $entityManager)
    {
        $description = $request->query->get('description') ?? null;
        if ($description !== null &&  strlen($description) >= 4) {
            $author = $entityManager->createQuery(
                'Select a from ' . Author::class . ' a where a.last_name 
            like :description or a.first_name like :description'
            )
                ->setMaxResults("10")
                ->setParameter('description', $description . "%");

            $authorQt = $entityManager->createQuery(
                'Select count(a) from ' . Author::class . ' a where a.last_name 
            like :description or a.first_name like :description'
            )
                ->setMaxResults("10")
                ->setParameter('description', $description . "%");
            $qt = $authorQt->getSingleScalarResult();

            $book = $entityManager->createQuery(
                'Select b.id, b.title from ' . Book::class . ' b where b.title 
            like :description'
            )
                ->setMaxResults(10 - $qt >= 0 ? 10 - $qt >= 0 : 0)
                ->setParameter('description', $description . "%");

            return ["author" => $author->getResult(),
                    "books" =>  $book->getResult()];
        }
    }

    /**
    * -----------------------------------------------------------------------------------------------
    */

    #[OA\Get(
        summary: "Give recommendation of user to follow"
    )]
    #[OA\Response(
        response: 200,
        description: "List of user",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: "#/components/schemas/UserInfos",
            )
        )
    )]
    #[IsGranted("ROLE_USER")]
    #[Security(name: "Bearer")]
    #[View(serializerGroups: ['basic'])]
    #[Route("/userRecommendation", name: "_userRecommendation", methods: ["GET"])]
    public function userRecommendation(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager)
    {
        /*$queryBuilder = $entityManager->createQueryBuilder()
            ->select('c.name_category, COUNT(b.id) as loan_number')
            ->from(User::class, 'u')
            ->innerJoin('u.loans', 'l')
            ->innerJoin('l.book', 'b')
            ->innerJoin('b.categories','c')
            ->where("u.id = :id")
            ->groupBy("c.name_category")
            ->setParameter('id', $user->getId());
        $result = $queryBuilder->getQuery()->getResult();*/

        $friendList = [];
        foreach ($user->getFollows() as $u) {
            array_push($friendList, $u->getId());
        }

        $queryBuilder = $entityManager->createQueryBuilder();
        $ttest = $queryBuilder ->select('u')
            ->from(User::class, 'u')
            ->innerJoin('u.loans', 'l')
            ->where('l.user = u')
            ->andWhere($queryBuilder->expr()->notIn('u.id', $friendList))
            ->groupBy('u')
            ->orderBy('count(l)', 'DESC')
            ->setMaxResults("4");
        $result = $ttest->getQuery()->getResult();

        return $result;
    }
}
