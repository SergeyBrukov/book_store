<?php

namespace App\Services;

use App\Entity\DeliveryMethods;
use App\Repository\DeliveryPaymentMethodsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeliveryMethodsService
{
    public function __construct
    (
        private ValidatorInterface               $validator,
        private EntityManagerInterface           $entityManager,
        private DeliveryPaymentMethodsRepository $deliveryPaymentMethodsRepository,
        private SerializerInterface              $serializer
    )
    {
    }

    public function createDeliveryMethod($data): JsonResponse
    {
        $name = $data['name'];
        $deliveryPaymentMethodId = $data['deliveryPaymentMethodId'];

        $deliveryPaymentMethodElement = $this->deliveryPaymentMethodsRepository->findOneBy(['id' => $deliveryPaymentMethodId]);

        if (!$deliveryPaymentMethodElement) {
            return new JsonResponse("Payment method not found", JsonResponse::HTTP_BAD_REQUEST);
        }

        $newDeliveryMethod = new DeliveryMethods();
        $newDeliveryMethod
            ->setName($name)
            ->addDeliveryPaymentMethod($deliveryPaymentMethodElement);

        $errors = $this->validator->validate($newDeliveryMethod, null);

        if (count($errors) > 0) {
            $errorsData = [];

            foreach ($errors as $error) {
                $errorsData[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorsData], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($newDeliveryMethod);
        $this->entityManager->flush();

        $serializerData = $this->serializer->serialize($newDeliveryMethod, 'json', ['groups' => ['info:deliveryMethod']]);

        return new JsonResponse(json_decode($serializerData), JsonResponse::HTTP_CREATED);
    }
}