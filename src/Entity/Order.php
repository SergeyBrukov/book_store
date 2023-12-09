<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\OrderController;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate         : 'get-orders',
            status              : JsonResponse::HTTP_OK,
            normalizationContext: ['groups' => 'collection-info:order'],
            security            : "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            routeName           : 'get-order-id',
            status              : JsonResponse::HTTP_OK,
            controller          : OrderController::class,
            normalizationContext: ['groups' => ['info:order']],
            security            : "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')"
        ),
        new Post(
            routeName             : 'create-order',
            status                : JsonResponse::HTTP_CREATED,
            controller            : OrderController::class,
            normalizationContext  : ['groups' => ['info:order']],
            denormalizationContext: ['groups' => ['create:order']],
            security              : "is_granted('ROLE_USER')"
        ),
        new Delete(
            routeName : 'delete-order',
            status    : JsonResponse::HTTP_NO_CONTENT,
            controller: OrderController::class,
            security  : "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id'             => 'partial', 'owner.firstName' => 'partial',
    'owner.lastName' => 'partial'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'deliveryDate'])]
#[ApiFilter(RangeFilter::class, properties: ['totalAmount'])]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info:order', 'collection-info:order'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryMethodId')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['create:order', 'info:order'])]
    #[ApiProperty(
        openapiContext: [
            'type'    => 'string',
            'format'  => 'string',
            'example' => 'id'
        ]
    )]
    private ?DeliveryMethods $deliveryMethod = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create:order', 'info:order'])]
    private ?string $userName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create:order', 'info:order'])]
    private ?string $town = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create:order', 'info:order'])]
    private ?string $city = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    #[Groups(['create:order', 'info:order', 'collection-info:order'])]
    #[ApiProperty(
        openapiContext: [
            'type'   => 'string',
            'format' => 'timestamp'
        ]
    )]
    private ?\DateTimeInterface $deliveryDate;

    #[ORM\Column]
    #[Groups(['create:order', 'info:order'])]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
        ]
    )]
    private ?int $telephone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create:order', 'info:order'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['create:order', 'info:order', 'collection-info:order'])]
    private ?string $orderComment = null;

    #[ORM\ManyToOne(inversedBy: 'deliveryPaymentMethodId')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['create:order', 'info:order'])]
    #[ApiProperty(
        openapiContext: [
            'type'    => 'string',
            'example' => 'id'
        ]
    )]
    private ?DeliveryPaymentMethods $paymentMethod = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['collection-info:order'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'orderProduct', targetEntity: OrderItem::class, cascade: ['remove'])]
    #[Groups(['create:order'])]
    #[ApiProperty(
        openapiContext: [
            'type'    => 'array',
            'example' => ['id']
        ]
    )]
    private Collection $orderProductItem;

    #[ORM\Column]
    #[Groups(['info:order', 'collection-info:order'])]
    private ?int $totalItems = null;

    #[ORM\Column]
    #[Groups(['info:order', 'collection-info:order'])]
    private ?float $totalAmount = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[Groups(['info:order', 'collection-info:order'])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->orderProductItem = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryMethod(): ?DeliveryMethods
    {
        return $this->deliveryMethod;
    }

    public function setDeliveryMethod(?DeliveryMethods $delivery_method): static
    {
        $this->deliveryMethod = $delivery_method;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $user_name): static
    {
        $this->userName = $user_name;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(string $town): static
    {
        $this->town = $town;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $delivery_date): static
    {
        $this->deliveryDate = $delivery_date;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getOrderComment(): ?string
    {
        return $this->orderComment;
    }

    public function setOrderComment(?string $order_comment): static
    {
        $this->orderComment = $order_comment;

        return $this;
    }

    public function getPaymentMethod(): ?DeliveryPaymentMethods
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?DeliveryPaymentMethods $payment_method): static
    {
        $this->paymentMethod = $payment_method;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->createdAt = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderProductItem(): Collection
    {
        return $this->orderProductItem;
    }

    public function addOrderProductItem(OrderItem $orderProductItem): static
    {
        if (!$this->orderProductItem->contains($orderProductItem)) {
            $this->orderProductItem->add($orderProductItem);
            $orderProductItem->setOrderProduct($this);
        }

        return $this;
    }

    public function removeOrderProductItem(OrderItem $orderProductItem): static
    {
        if ($this->orderProductItem->removeElement($orderProductItem)) {
            // set the owning side to null (unless already changed)
            if ($orderProductItem->getOrderProduct() === $this) {
                $orderProductItem->setOrderProduct(null);
            }
        }

        return $this;
    }

    public function getTotalItems(): ?int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $totalItems): static
    {
        $this->totalItems = $totalItems;

        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
