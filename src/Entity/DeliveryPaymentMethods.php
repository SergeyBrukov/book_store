<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\DeliveryPaymentMethodsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DeliveryPaymentMethodsRepository::class)]
#[GetCollection(
    uriTemplate         : "delivery-payment-methods",
    normalizationContext: ['groups' => ['info:deliveryPaymentMethods']],
    security            : "is_granted('ROLE_ADMIN')"
)]
#[Post(
    uriTemplate           : "create-delivery-payment-method",
    normalizationContext  : ['groups' => ['info:deliveryPaymentMethods']],
    denormalizationContext: ['groups' => ['create:deliveryPaymentMethods']],
    security              : "is_granted('ROLE_ADMIN')"
)]
#[Patch(
    uriTemplate           : "edit-delivery-payment-method/{id}",
    normalizationContext  : ['groups' => ['info:deliveryPaymentMethods']],
    denormalizationContext: ['groups' => ['patch:deliveryPaymentMethods']],
    security              : "is_granted('ROLE_ADMIN')"
)]
#[Delete(
    uriTemplate: "delete-delivery-payment-method/{id}",
    security   : "is_granted('ROLE_ADMIN')"
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'commission'])]
class DeliveryPaymentMethods
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info:deliveryPaymentMethods', 'info:order'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create:deliveryPaymentMethods', 'info:deliveryPaymentMethods', 'patch:deliveryPaymentMethods', 'info:order'])]
    private string $name;

    #[ORM\ManyToMany(targetEntity: DeliveryMethods::class, inversedBy: 'deliveryPaymentMethods')]
    private Collection $delivery_method;

    #[ORM\Column]
    #[Groups(['create:deliveryPaymentMethods', 'info:deliveryPaymentMethods', 'patch:deliveryPaymentMethods'])]
    private float $commission = 0;

    public function __construct()
    {
        $this->delivery_method = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, DeliveryMethods>
     */
    public function getDeliveryMethod(): Collection
    {
        return $this->delivery_method;
    }

    public function addDeliveryMethod(DeliveryMethods $deliveryMethod): static
    {
        if (!$this->delivery_method->contains($deliveryMethod)) {
            $this->delivery_method->add($deliveryMethod);
        }

        return $this;
    }

    public function removeDeliveryMethod(DeliveryMethods $deliveryMethod): static
    {
        $this->delivery_method->removeElement($deliveryMethod);

        return $this;
    }

    public function getCommission(): ?float
    {
        return $this->commission;
    }

    public function setCommission(float $commission): static
    {
        $this->commission = $commission;

        return $this;
    }
}
