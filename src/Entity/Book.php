<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\BookController;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            routeName             : 'app_create_book',
            status                : JsonResponse::HTTP_CREATED,
            controller            : BookController::class,
            denormalizationContext: ['groups' => ['create:book']],
            security              : "is_granted('ROLE_USER')",
            name                  : "Create book"
        ),
        new GetCollection(
            uriTemplate                 : 'get-books',
            status                      : JsonResponse::HTTP_OK,
            paginationClientItemsPerPage: true,
            normalizationContext        : ['groups' => ['info:book']],
            security                    : "is_granted('ROLE_USER')",
            name                        : 'Books info'
        )
    ]
)]
#[Get(normalizationContext: ['groups' => ['info:book']], security: "is_granted('ROLE_USER')")]
#[Delete(security: "is_granted('ROLE_USER')")]
#[Patch(
    normalizationContext: ['groups' => ['info:book']],
    denormalizationContext: ['groups' => ['update:book']],
    security: "is_granted('ROLE_USER')"
)]
#[ApiFilter(OrderFilter::class, properties: ['id' => 'ASC', 'name' => 'DESC'])]
#[ApiFilter(SearchFilter::class, properties: [
    'id'               => 'partial',
    'name'             => 'partial',
    'author.firstName' => 'partial'
])]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(DateFilter::class, properties: ['created_at' => DateFilter::EXCLUDE_NULL])]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info:book', 'info:basketItem', 'user:response'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Length(min: 5, groups: ['create:book'])]
    #[Groups(['create:book', 'info:book', 'update:book', 'info:basketItem'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Length(min: 5, groups: ['create:book'])]
    #[Groups(['create:book', 'info:book', 'update:book'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['create:book', 'info:book', 'update:book', 'info:basketItem'])]
    private int $price = 0;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'books')]
    private ?User $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['info:book'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['update:book'])]
    private ?int $sale = null;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getSale(): ?int
    {
        return $this->sale;
    }

    public function setSale(?int $sale): self
    {
        $this->sale = $sale;

        return $this;
    }
}
