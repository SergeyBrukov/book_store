<?php

namespace App\Services;

use App\Entity\DeliveryPaymentMethods;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\BasketItemRepository;
use App\Repository\BasketRepository;
use App\Repository\DeliveryMethodsRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class OrderService
{

    public function __construct
    (
        private readonly PdfGeneratorService       $pdfGeneratorService,
        private readonly DeliveryMethodsRepository $deliveryMethodsRepository,
        private readonly BasketItemRepository      $basketItemRepository,
        private readonly BasketRepository          $basketRepository,
        private readonly EntityManagerInterface    $entityManager,
        private readonly SerializerInterface       $serializer,
        private readonly UserRepository            $userRepository,
        private readonly OrderRepository           $orderRepository,
        private readonly Environment               $twig,
        private readonly TokenService              $tokenService
    )
    {
    }

    /**
     * @param array{
     *     deliveryMethod: string,
     *     orderProductItem: array<int>,
     *     userName: string,
     *     town: string,
     *     city: string,
     *     deliveryDate: string,
     *     telephone: string,
     *     email: string,
     *     orderComment: string,
     *     paymentMethod: string
     * } $orderData
     * @param User $user
     * @return JsonResponse
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function createOrder(array $orderData, User $user): mixed
    {
        $deliveryMethod = $this->deliveryMethodsRepository->find($orderData['deliveryMethod']);

        $basket = $this->basketRepository->findOneBy(['user' => $user->getId()]);

        if (!$deliveryMethod) {
            return new JsonResponse(['message' => 'Delivery method not exist'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newOrder = new Order();

        $newOrder->setDeliveryMethod($deliveryMethod);

        $orderItems = $orderData['orderProductItem'];

        $orderTotalCount = 0;
        $orderTotalAmount = 0;

        foreach ($orderItems as $orderItem) {

            $existOrderItem = $this->basketItemRepository->find($orderItem);

            if ($existOrderItem) {
                $newOrderItem = new OrderItem();
                $newOrderItem
                    ->setCount($existOrderItem->getCount())
                    ->setSumm($existOrderItem->getCount() * $existOrderItem->getBookInfo()->getPrice())
                    ->setProduct($existOrderItem->getBookInfo());
                $this->entityManager->persist($newOrderItem);

                $orderTotalCount = $orderTotalCount + $existOrderItem->getCount();
                $orderTotalAmount = $orderTotalAmount + ($existOrderItem->getCount() * $existOrderItem->getBookInfo()->getPrice());

                $basket->removeBasketItem($existOrderItem);
                $this->entityManager->remove($existOrderItem);

                $newOrder->addOrderProductItem($newOrderItem);
            } else {
                return new JsonResponse(['message' => 'Product not exist'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $paymentMethod = $this->entityManager->getRepository(DeliveryPaymentMethods::class)->find($orderData['paymentMethod']);

        $date = new \DateTime($orderData['deliveryDate']);

        $newOrder
            ->setTelephone($orderData['telephone'])
            ->setUserName($orderData['userName'])
            ->setEmail($orderData['email'])
            ->setTown($orderData['town'])
            ->setCity($orderData['city'])
            ->setDeliveryDate($date)
            ->setPaymentMethod($paymentMethod)
            ->setOrderComment($orderData['orderComment'])
            ->setTotalAmount($orderTotalAmount)
            ->setTotalItems($orderTotalCount)
            ->setOwner($user);

        $this->entityManager->persist($newOrder);
        $this->entityManager->flush();

        $orderID = $newOrder->getId();

        $content = $this->twig->render('order-template.html.twig', ['orderData' => $newOrder]);

        $this->pdfGeneratorService->generateAndSavePdf($content, "order-$orderID.pdf");

        $orderSerializedData = $this->serializer->serialize($newOrder, 'json', ['groups' => ['info:order']]);

        return json_decode($orderSerializedData);
    }

    /**
     * @param string $orderId
     * @return mixed
     */
    public function getOrderById(string $orderId): mixed
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            return new JsonResponse("Order not found", JsonResponse::HTTP_FORBIDDEN);
        }

        $orderSerializeData = $this->serializer->serialize($order, 'json', ['groups' => ['info:order']]);

        return json_decode($orderSerializeData);
    }


    /**
     * @param string $orderId
     * @return mixed
     */
    public function deleteOrderById(string $orderId): mixed
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            return new JsonResponse("Order $orderId not found", JsonResponse::HTTP_FORBIDDEN);
        }

        if ($this->tokenService->getRoles()[0] === User::ROLE_ADMIN) {
            $this->entityManager->remove($order);
            $this->entityManager->flush();

            return new JsonResponse('You remove order', JsonResponse::HTTP_NO_CONTENT);
        }

        $examinationOwner = $order->getOwner()->getId() === intval($this->tokenService->getId());

        if (!$examinationOwner) {
            return new JsonResponse("This not your order", JsonResponse::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return 'You remove order';
    }
}