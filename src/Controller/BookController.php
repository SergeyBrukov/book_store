<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Services\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @var BookService
     */
    private BookService $bookService;


    /**
     * @param BookService $bookService
     */
    public function __construct
    (
        BookService $bookService
    )
    {
        $this->bookService = $bookService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/create-book', name: 'app_create_book', methods: ['POST'])]
    public function createNewBook(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $bookData = [
            'name'        => $request->request->get('name'),
            'price'       => $request->request->get('price'),
            'description' => $request->request->get('description'),
            'imageFile' => $request->files->get('imageFile')
        ];

        return new JsonResponse($this->bookService->createNewBool($bookData, $user), JsonResponse::HTTP_CREATED);
    }
}
