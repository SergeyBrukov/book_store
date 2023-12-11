<?php

namespace App\Controller;

use App\Services\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $userIdentifier = $this->getUser()->getUserIdentifier();

        return $this->commentService->createComment($data, $userIdentifier);
    }
}
