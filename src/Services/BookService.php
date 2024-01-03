<?php

namespace App\Services;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Book;
use App\Entity\MediaFiles;
use App\Entity\User;
use App\Filter\CustomBookFilter;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookService
{
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator,
        private readonly SerializerInterface    $serializer,
        private readonly FileServices           $fileServices,
        private readonly BookRepository         $bookRepository,
        private readonly CustomBookFilter       $customBookFilter
    )
    {
    }

    /**
     * @param array{
     *     name: string,
     *     price: int,
     *     description: string,
     *     imageFile: UploadedFile } $bookData
     * @param User $user
     * @return mixed
     */
    public function createNewBool(array $bookData, User $user): mixed
    {

        $book = new Book();
        $mediaFile = new MediaFiles();

        $book
            ->setName($bookData['name'])
            ->setPrice($bookData['price'])
            ->setDescription($bookData['description'])
            ->setAuthor($user);

        $errors = $this->validator->validate($book, null, ['create:book']);

        if (count($errors) > 0) {
            $errorData = [];

            foreach ($errors as $error) {
                $errorData[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorData], JsonResponse::HTTP_BAD_REQUEST);
        }

        $fileName = md5(uniqid()) . '.' . $bookData['imageFile']->guessExtension();

        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $fileFolder = $book->getId();

        $mediaFile
            ->setBook($book)
            ->setFileSize($bookData['imageFile']->getSize())
            ->setFileName($bookData['imageFile']->getClientOriginalName())
            ->setFolder("storage/books/$fileFolder")
            ->setFilePath($fileName)
            ->setFileFormat($bookData['imageFile']->getMimeType());

        $this->fileServices->saveImage($fileFolder, $bookData['imageFile'], $fileName, 'books');
        $this->entityManager->persist($mediaFile);
        $book->addImage($mediaFile);
        $this->entityManager->flush();

        $serializerBook = $this->serializer->serialize($book, 'json', ['groups' => ['info:book']]);

        return json_decode($serializerBook);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return mixed
     */
    function getMyBooks(User $user, Request $request): mixed
    {
        $queryBuilder = $this->bookRepository->createQueryBuilder('o');

        $queryBuilder
            ->andWhere('o.author = :author')
            ->setParameter('author', $user->getId());

        $context = ['filters' => $request->query->all()];
        $this->customBookFilter->apply($queryBuilder, new QueryNameGenerator(), Book::class, null, $context);

        $books = $queryBuilder->getQuery()->getResult();

        $serializerBooks = $this->serializer->serialize($books, 'json', ['groups' => ['info:book']]);

        return json_decode($serializerBooks);
    }
}