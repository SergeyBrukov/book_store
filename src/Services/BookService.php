<?php

namespace App\Services;

use App\Entity\Book;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookService
{
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly ValidatorInterface     $validator,
        private readonly SerializerInterface    $serializer
    )
    {
    }

    /**
     * @param Request $request
     * @param string $userIdentifier
     * @return JsonResponse
     */
    public function createNewBool(Request $request, string $userIdentifier): JsonResponse
    {

        $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

        $bookData = json_decode($request->getContent(), true);

        $book = new Book();
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

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $serializerBook = $this->serializer->serialize($book, 'json', ['groups' => ['info:book']]);

        return new JsonResponse(json_decode($serializerBook), JsonResponse::HTTP_CREATED);

    }
}