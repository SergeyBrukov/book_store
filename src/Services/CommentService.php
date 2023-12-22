<?php

namespace App\Services;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentService
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param OrderRepository $orderRepository
     * @param BookRepository $bookRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository        $orderRepository,
        private readonly BookRepository         $bookRepository,
        private readonly UserRepository         $userRepository,
        private readonly SerializerInterface    $serializer,
        private readonly ValidatorInterface     $validator
    )
    {
    }

    /**
     * @param array{
     *     message: string,
     *     book: string,
     *     orderId: int
     * } $commentData
     * @param User $user
     * @return mixed
     */
    public function createComment(array $commentData, User $user): mixed
    {
        $orderExits = $this->orderRepository->find($commentData['orderId']);

        if (!$orderExits) {
            return new JsonResponse(['message' => 'Order by #' . $commentData['orderId'] . 'not exist'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book = $this->bookRepository->find($commentData['book']);

        if (!$book) {
            return new JsonResponse(['message' => 'Book by #' . $commentData['book'] . 'not exist'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $comment = new Comment();

        $comment
            ->setBook($book)
            ->setAuthor($user)
            ->setMessage($commentData['message'])
            ->setOrderId($commentData['orderId']);

        $errors = $this->validator->validate($comment, null, ['create:comment']);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorData = [];

                $errorData[$error->getPropertyPath()] = $error->getMessage();

                return new JsonResponse(['errors' => $errorData], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        $serializedCommentData = $this->serializer->serialize($comment, 'json', ['groups' => ['info:comment']]);

        return json_decode($serializedCommentData);
    }
}