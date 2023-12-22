<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class OrderController extends AbstractController
{

    /**
     * @param OrderService $orderService
     */
    public function __construct
    (
        private readonly OrderService $orderService
    )
    {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/api/create-order', name: 'create-order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getUser();

        return new JsonResponse($this->orderService->createOrder($data, $user), JsonResponse::HTTP_CREATED);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/get-order/{id}', name: 'get-order-id', methods: ['GET'])]
    public function getOrderById(string $id): JsonResponse
    {
        return new JsonResponse($this->orderService->getOrderById($id), JsonResponse::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/delete-order/${id}', name: 'delete-order', methods: ['DELETE'])]
    public function deleteOrderById(string $id): JsonResponse
    {
        return new JsonResponse($this->orderService->deleteOrderById($id), JsonResponse::HTTP_NO_CONTENT);
    }
}
