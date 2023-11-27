<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\BasketItem;
use App\Entity\Book;
use App\Services\BasketItemService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketItemController extends AbstractController
{
    public function __construct(
        private BasketItemService $basketItemService
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
        $userIdentifier = $this->getUser()->getUserIdentifier();

        return $this->basketItemService->addBookInBasket($data, $userIdentifier);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/decrement-book-from-basket/{id}', name: 'decrement-book-from-basket', methods: ['PATCH'])]
    public function decrementBookFromBasket(string $id): JsonResponse
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();

        return $this->basketItemService->decrementBookFromBasket($id, $userIdentifier);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/delete-book-from-basket/{id}', name: 'delete-book-from-basket', methods: ['DELETE'])]
    public function deleteBookFromBasket(string $id): JsonResponse
    {
        $userIdentifier = $this->getUser()->getUserIdentifier();

        return $this->basketItemService->deleteBookBasket($id, $userIdentifier);
    }
}
