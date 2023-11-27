<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate         : 'basket-info/{id}',
            status              : JsonResponse::HTTP_OK,
            normalizationContext: ['groups' => ['basket-info']],
            security            : "is_granted('ROLE_USER')",
            name                : 'Basket info by user'
        )
    ]
)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:response'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['user:response', 'basket-info'])]
    private ?int $totalCount = null;

    #[ORM\Column]
    #[Groups(['user:response', 'basket-info'])]
    private ?int $totalAmount = null;

    #[ORM\OneToOne(inversedBy: 'basket', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'basket', targetEntity: BasketItem::class)]
    #[Groups(['user:response'])]
    private Collection $basket_items;

    public function __construct()
    {
        $this->setTotalAmount(0);
        $this->setTotalCount(0);
        $this->basket_items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }

    public function setTotalCount(int $totalCount): static
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(int $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, BasketItem>
     */
    public function getBasketItems(): Collection
    {
        return $this->basket_items;
    }

    public function addBasketItem(BasketItem $basketItem): static
    {
        if (!$this->basket_items->contains($basketItem)) {
            $this->basket_items->add($basketItem);
            $basketItem->setBasket($this);
        }

        return $this;
    }

    public function removeBasketItem(BasketItem $basketItem): static
    {
        if ($this->basket_items->removeElement($basketItem)) {
            // set the owning side to null (unless already changed)
            if ($basketItem->getBasket() === $this) {
                $basketItem->setBasket(null);
            }
        }

        return $this;
    }
}
