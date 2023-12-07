<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\BasketItemController;
use App\Repository\BasketItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BasketItemRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            routeName             : 'add-book-in-basket',
            status                : JsonResponse::HTTP_CREATED,
            controller            : BasketItemController::class,
            normalizationContext  : ['groups' => ['info:basketItem']],
            denormalizationContext: ['groups' => ['create:basketItem']],
            security              : "is_granted('ROLE_USER')",
            name                  : "Add book in basket"
        ),
        new Patch(
            routeName: 'decrement-book-from-basket',
            status: JsonResponse::HTTP_OK,
            controller: BasketItemController::class,
            denormalizationContext: ['groups' => ['decrement:']],
            security: "is_granted('ROLE_USER')",
            name: "Decrement book from basket"
        ),
        new Delete(
            routeName: 'delete-book-from-basket',
            status: JsonResponse::HTTP_NO_CONTENT,
            controller: BasketItemController::class,
            security: "is_granted('ROLE_USER)",
            name: "Delete book"
        )
    ]
)]


class BasketItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info:basketItem', 'user:response'])]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[ApiProperty(example: "bookId")]
    #[Groups(['create:basketItem', 'info:basketItem'])]
    private ?Book $bookInfo = null;

    #[ORM\Column]
    #[Groups(['create:basketItem', 'info:basketItem'])]
    private int $count = 1;

    #[ORM\ManyToOne(inversedBy: 'basket_items')]
    #[ApiProperty(example: "basketId")]
    private ?Basket $basket = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookInfo(): ?Book
    {
        return $this->bookInfo;
    }

    public function setBookInfo(?Book $bookInfo): static
    {
        $this->bookInfo = $bookInfo;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }
}
