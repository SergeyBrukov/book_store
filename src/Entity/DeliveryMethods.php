<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\DeliveryMethodsController;
use App\Repository\DeliveryMethodsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: DeliveryMethodsRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'This name is already in use.')]
#[ApiResource(
    operations: [
        new Post(
            routeName             : "create-delivery-method",
            status                : JsonResponse::HTTP_CREATED,
            controller            : DeliveryMethodsController::class,
            normalizationContext  : ['groups' => ['info:deliveryMethod']],
            denormalizationContext: ['groups' => ['create:deliveryMethod']],
            security              : "is_granted('ROLE_ADMIN')",
            name                  : "Create delivery method"
        ),
        new GetCollection(
            uriTemplate: "delivery-method",
            status     : JsonResponse::HTTP_OK,
            security   :"is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')"
        )
    ]
)]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'DESC'])]
class DeliveryMethods
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info:deliveryMethod', 'info:order'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['create:deliveryMethod', 'info:deliveryMethod', 'info:order'])]
    #[Length(min: 5, minMessage: "Min length 5 symbol")]
    private string $name;

    #[Groups(['create:deliveryMethod'])]
    private int $deliveryPaymentMethodId;

    #[ORM\ManyToMany(targetEntity: DeliveryPaymentMethods::class, mappedBy: 'delivery_method')]
    private Collection $deliveryPaymentMethods;

    public function __construct()
    {
        $this->deliveryPaymentMethods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, DeliveryPaymentMethods>
     */
    public function getDeliveryPaymentMethods(): Collection
    {
        return $this->deliveryPaymentMethods;
    }

    public function addDeliveryPaymentMethod(DeliveryPaymentMethods $deliveryPaymentMethod): static
    {
        if (!$this->deliveryPaymentMethods->contains($deliveryPaymentMethod)) {
            $this->deliveryPaymentMethods->add($deliveryPaymentMethod);
            $deliveryPaymentMethod->addDeliveryMethod($this);
        }

        return $this;
    }

    public function removeDeliveryPaymentMethod(DeliveryPaymentMethods $deliveryPaymentMethod): static
    {
        if ($this->deliveryPaymentMethods->removeElement($deliveryPaymentMethod)) {
            $deliveryPaymentMethod->removeDeliveryMethod($this);
        }

        return $this;
    }

    public function getDeliveryPaymentMethodId(): int
    {
        return $this->deliveryPaymentMethodId;
    }

    public function setDeliveryPaymentMethodId(int $deliveryPaymentMethodId): static
    {
        $this->deliveryPaymentMethodId = $deliveryPaymentMethodId;

        return $this;
    }
}
