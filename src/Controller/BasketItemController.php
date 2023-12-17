<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\BasketItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BasketItemController extends AbstractController
{
    public function __construct(
        private readonly BasketItemService $basketItemService
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/add-book-in-basket', name: 'add-book-in-basket', methods: ['POST'])]
    public function addBookInBasket(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse($this->basketItemService->addBookInBasket($data, $user), JsonResponse::HTTP_CREATED);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/decrement-book-from-basket/{id}', name: 'decrement-book-from-basket', methods: ['PATCH'])]
    public function decrementBookFromBasket(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse($this->basketItemService->decrementBookFromBasket($id, $user), JsonResponse::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/delete-book-from-basket/{id}', name: 'delete-book-from-basket', methods: ['DELETE'])]
    public function deleteBookFromBasket(string $id): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse($this->basketItemService->deleteBookBasket($id, $user), JsonResponse::HTTP_OK);
    }
}
