<?php

namespace App\Services;

use App\Entity\Book;
use App\Entity\MediaFiles;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookService
{
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface     $validator,
        private readonly SerializerInterface    $serializer,
        private readonly FileServices           $fileServices
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

        $mediaFile
            ->setBook($book)
            ->setFileSize($bookData['imageFile']->getSize())
            ->setFileName($bookData['imageFile']->getClientOriginalName())
            ->setFolder(__DIR__)
            ->setFilePath($fileName)
            ->setFileFormat($bookData['imageFile']->getMimeType());

        $this->fileServices->saveImage($bookData['imageFile'], $fileName);
        $this->entityManager->persist($mediaFile);
        $book->addImage($mediaFile);
        $this->entityManager->flush();

        $serializerBook = $this->serializer->serialize($book, 'json', ['groups' => ['info:book']]);

        return json_decode($serializerBook);

    }
}