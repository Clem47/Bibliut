<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Editor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use GuzzleHttp\Client;
use Faker\Factory;

class BookAuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $faker = Factory::create();
        $alphabet = range('a', 'z');
        // Iterate over the alphabet
        foreach ($alphabet as $letter) {
            $query = $letter;
            $maxResults = 40;
            $response = $client->get('https://www.googleapis.com/books/v1/volumes', [
                'query' => [
                    'q' => $query,
                    'maxResults' => $maxResults,
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $existingBook = $manager->getRepository(Book::class)->findOneBy([
                        'title' => $item['volumeInfo']['title'],
                    ]);
                    if (
                        !$existingBook && isset($item['volumeInfo']['title'])
                        && isset($item['volumeInfo']['publishedDate'])
                    ) {
                        $book = new Book();
                        $book->setTitle($item['volumeInfo']['title']);
                        $book->setReleaseDate(
                            new \DateTime(
                                str_replace(
                                    "*",
                                    "",
                                    $item['volumeInfo']['publishedDate']
                                )
                            )
                        );
                        if (isset($item['volumeInfo']['pageCount'])) {
                            $book->setNbPages($item['volumeInfo']['pageCount']);
                        }
                        if (isset($item['volumeInfo']['imageLinks']['thumbnail'])) {
                            $book->setImage($item['volumeInfo']['imageLinks']['thumbnail']);
                        }
                        if (isset($item['volumeInfo']['description'])) {
                            $book->setSummary($item['volumeInfo']['description']);
                        }
                        $book->setAcquisitionDate($faker->dateTimeBetween('-1 month', 'now'));
                        $authors = $item['volumeInfo']['authors'] ?? [];
                        foreach ($authors as $authorName) {
                            if (false === strpos($authorName, ' ')) {
                                continue;
                            }
                            $matches = [];
                            preg_match('/^(?<first_name>.*?) (?<last_name>[^\s]+)$/', $authorName, $matches);
                            $firstName = $matches['first_name'] ?? '';
                            $lastName = $matches['last_name'] ?? '';
                            $firstName = trim($firstName);
                            $lastName = trim($lastName);
                            $existingAuthor = $manager->getRepository(Author::class)->findOneBy([
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                            ]);
                            if (!$existingAuthor) {
                                $author = new Author();
                                $author->setFirstName($firstName);
                                $author->setLastName($lastName);
                                $author->addBook($book);
                                $manager->persist($author);
                            } else {
                                $existingAuthor->addBook($book);
                            }
                        }
                        $categories = $item['volumeInfo']['categories'] ?? [];
                        foreach ($categories as $categoryName) {
                            $categoryName = trim($categoryName);
                            $categoryName = preg_replace('/\p{C}/u', '', $categoryName);
                            $existingCategory = $manager->getRepository(Category::class)->findOneBy([
                                'name_category' => $categoryName,
                            ]);
                            if (!$existingCategory) {
                                $category = new Category();
                                $category->setNameCategory($categoryName);
                                $category->addBook($book);
                                $manager->persist($category);
                            } else {
                                $existingCategory->addBook($book);
                            }
                        }
                        $languageName = $item['volumeInfo']['language'] ?? '';
                        $languageName = trim($languageName);
                        $existingLanguage = $manager->getRepository(Language::class)->findOneBy([
                            'name_language' => $languageName,
                        ]);
                        if (!$existingLanguage) {
                            $language = new Language();
                            $language->setNameLanguage($languageName);
                            $language->addBook($book);
                            $manager->persist($language);
                        } else {
                            $existingLanguage->addBook($book);
                        }
                        $editorName = $item['volumeInfo']['publisher'] ?? '';
                        $editorName = trim($editorName);
                        $existingEditor = $manager->getRepository(Editor::class)->findOneBy([
                            'name_editor' => $editorName,
                        ]);
                        if (!$existingEditor) {
                            $editor = new Editor();
                            $editor->setNameEditor($editorName);
                            $editor->addBook($book);
                            $manager->persist($editor);
                        } else {
                            $existingEditor->addBook($book);
                        }
                        $manager->persist($book);
                    }
                    $manager->flush();
                }
            }
        }
    }
}
