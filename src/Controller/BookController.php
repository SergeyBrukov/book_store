<?php

namespace App\Controller;

use App\Entity\Book;
use App\Services\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    public function __construct
    (
        private BookService $bookService
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/create-book', name: 'app_create_book', methods: ['POST'])]
    public function createNewBook(Request $request): JsonResponse
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();

        return $this->bookService->createNewBool($request, $userIdentifier);
    }
}
