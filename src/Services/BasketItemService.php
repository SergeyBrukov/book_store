<?php

namespace App\Services;

use App\Entity\Basket;
use App\Entity\BasketItem;
use App\Entity\User;
use App\Repository\BasketRepository;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class BasketItemService
{
    /**
     * @param BasketRepository $basketRepository
     * @param BookRepository $bookRepository
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param LoggerService $loggerService
     */
    public function __construct(
        private readonly BasketRepository       $basketRepository,
        private readonly BookRepository         $bookRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly SerializerInterface    $serializer,
        private readonly LoggerService          $loggerService
    )
    {
    }

    /**
     * @param $data
     * @param User $user
     * @return array
     */
    public function addBookInBasket($data, User $user): array
    {

        $book = $this->bookRepository->find($data['bookInfo']);

        $basket = $user->getBasket();

        $basketItems = $basket->getBasketItems();

        if ($basketItems->count() > 0) {
            foreach ($basketItems as $basketItemExists) {
                if ($basketItemExists->getBookInfo()->getId() === intval($data['bookInfo'])) {
                    $basketItemExists->setCount($basketItemExists->getCount() + $data['count']);
                    $this->incrementBasketItem($basket, $data['count'], $book->getPrice());
                    $this->entityManager->flush();

                    $serializedBasketItemInfo = $this->serializer->serialize($basketItemExists, 'json', ['groups' => ['info:basketItem']]);

                    $this->loggerService->createLog('Add new basket item', LoggerService::LOG__SUCCESS);

                    return ['item' => json_decode($serializedBasketItemInfo)];
                }
            }
        }

        $basketItem = new BasketItem();

        $basketItem
            ->setCount($data['count'])
            ->setBasket($basket)
            ->setBookInfo($book);

        $this->entityManager->persist($basketItem);

        $this->incrementBasketItem($basket, $data['count'], $book->getPrice());

        $this->entityManager->flush();

        $serializedBasketItemInfo = $this->serializer->serialize($basketItem, 'json', ['groups' => ['info:basketItem']]);

        $this->loggerService->createLog('Add new basket item', LoggerService::LOG__SUCCESS);

        return ['item' => json_decode($serializedBasketItemInfo)];
    }

    /**
     * @param string $basketItemId
     * @param User $user
     * @return string|JsonResponse
     */
    public function decrementBookFromBasket(string $basketItemId, User $user): string|JsonResponse
    {
        $basket = $user->getBasket();

        if (count($basket->getBasketItems()) === 0) {
            return new JsonResponse('Basket empty, so you can not use this route.', JsonResponse::HTTP_BAD_REQUEST);
        }

        $basketItemById = null;

        foreach ($basket->getBasketItems() as $basketItem) {
            if ($basketItem->getId() === intval($basketItemId)) {
                $basketItemById = $basketItem;
                break;
            }
        }

        if (!$basketItemById) {
            return new JsonResponse("$basketItemId", JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->decrementBasketItem($basket, $basketItemById->getBookInfo()->getPrice());
        $basketItemById->setCount($basketItemById->getCount() - 1);
        $this->entityManager->flush();

        return "Succsess";
    }


    /**
     * @param string $basketItemId
     * @param User $user
     * @return string|JsonResponse
     */
    public function deleteBookBasket(string $basketItemId, User $user): string|JsonResponse
    {
        $basket = $user->getBasket();

        $basketItems = $basket->getBasketItems();

        $basketItemById = null;

        if (count($basketItems) === 0) {
            return new JsonResponse('Basket empty, so you can not use this route.', JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach ($basketItems as $basketItem) {
            if ($basketItem->getId() === intval($basketItemId)) {
                $basketItemById = $basketItem;
                break;
            }
        }

        $this->decrementBasketItem($basket, $basketItemById->getBookInfo()->getPrice(), $basketItemById->getCount());
        $basket->removeBasketItem($basketItemById);
        $this->entityManager->remove($basketItemById);
        $this->entityManager->flush();

        return "Succsess";
    }

    /**
     * @param Basket $basket
     * @param int $countBasketItem
     * @param int $priceBasketItem
     * @return void
     */
    private function incrementBasketItem(Basket $basket, int $countBasketItem, int $priceBasketItem): void
    {
        $totalBasketItemPrice = $countBasketItem * $priceBasketItem;

        $basket
            ->setTotalCount($basket->getTotalCount() + $countBasketItem)
            ->setTotalAmount($basket->getTotalAmount() + $totalBasketItemPrice);

    }

    /**
     * @param Basket $basket
     * @param int $priceBasketItem
     * @param int $basketItemCount
     * @return void
     */
    private function decrementBasketItem(Basket $basket, int $priceBasketItem, int $basketItemCount = 1): void
    {
        $totalBasketItemPrice = $priceBasketItem;

        if ($basketItemCount) {
            $totalBasketItemPrice = $basketItemCount * $priceBasketItem;
        }

        $basket
            ->setTotalAmount($basket->getTotalAmount() - $totalBasketItemPrice)
            ->setTotalCount($basket->getTotalCount() - $basketItemCount);
    }
}