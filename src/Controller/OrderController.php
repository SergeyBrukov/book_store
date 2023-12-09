<?php

namespace App\Controller;

use App\Services\OrderService;
use App\Services\TokenService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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

        $userIdentification = $this->getUser()->getUserIdentifier();

        return $this->orderService->createOrder($data, $userIdentification);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/get-order/{id}', name: 'get-order-id', methods: ['GET'] )]
    public function getOrderById(string $id): JsonResponse
    {
       return $this->orderService->getOrderById($id);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/api/delete-order/${id}', name: 'delete-order', methods: ['DELETE'])]
    public function deleteOrderById(string $id): JsonResponse
    {
        return $this->orderService->deleteOrderById($id);
    }
}
