<?php

namespace App\Controller;

use App\Services\DeliveryMethodsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryMethodsController extends AbstractController
{

    /**
     * @param DeliveryMethodsService $deliveryMethodsService
     */
    public function __construct(
        private DeliveryMethodsService $deliveryMethodsService
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/create-delivery-method', name: 'create-delivery-method', methods: ['POST'])]
    public function createDeliveryMethod(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $this->deliveryMethodsService->createDeliveryMethod($data);
    }
}
