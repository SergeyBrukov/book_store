<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

    public function __construct
    (
        private readonly CommentService $commentService
    )
    {
    }

    #[Route('/api/create-comment', name: 'create-comment-book', methods: ['POST'])]
    public function createComment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse($this->commentService->createComment($data, $user), JsonResponse::HTTP_CREATED);
    }
}
