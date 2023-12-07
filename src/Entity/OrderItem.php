<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $count = null;

    #[ORM\Column]
    private ?float $summ = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $product = null;

    #[ORM\ManyToOne(inversedBy: 'orderProductItem')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderProduct = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSumm(): ?float
    {
        return $this->summ;
    }

    public function setSumm(float $summ): static
    {
        $this->summ = $summ;

        return $this;
    }

    public function getProduct(): ?Book
    {
        return $this->product;
    }

    public function setProduct(?Book $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOrderProduct(): ?Order
    {
        return $this->orderProduct;
    }

    public function setOrderProduct(?Order $orderProduct): static
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }
}
