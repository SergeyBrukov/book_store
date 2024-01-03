<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\BookController;
use App\Filter\CustomBookFilter;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            inputFormats          : ['multipart' => ['multipart/form-data']],
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
            openapiContext              : [
                'parameters' => [
                    [
                        'name'        => 'name',
                        'in'          => 'query',
                        'description' => 'Book title',
                        'schema'      => [
                            'type' => 'string',
                        ],
                    ],
                    [
                        'name'        => 'price',
                        'in'          => 'query',
                        'description' => 'Book price',
                        'schema'      => [
                            'type' => 'number',
                        ],
                    ],
                ],
            ],
            paginationClientItemsPerPage: true,
            normalizationContext        : ['groups' => ['info:book']],
            filters                     : [CustomBookFilter::class],
            name                        : 'Books info'
        ),
        new GetCollection(
            routeName                   : 'app_get_my_books',
            status                      : JsonResponse::HTTP_OK,
            controller                  : BookController::class,
            openapiContext              : [
                'parameters' => [
                    [
                        'name'        => 'name',
                        'in'          => 'query',
                        'description' => 'Book title',
                        'schema'      => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
            paginationClientItemsPerPage: true,
            normalizationContext        : ['groups' => ['info:book']],
            name                        : 'My books info',
        )
    ]
)]
#[Get(normalizationContext: ['groups' => ['info-item:book']], security: "is_granted('ROLE_USER')")]
#[Delete(security: "is_granted('ROLE_USER')")]
#[Patch(
    normalizationContext  : ['groups' => ['info:book']],
    denormalizationContext: ['groups' => ['update:book']],
    security              : "is_granted('ROLE_USER')"
)]
//#[ApiFilter(OrderFilter::class, properties: ['id' => 'ASC', 'name' => 'DESC'])]
//#[ApiFilter(SearchFilter::class, properties: [
//    'id'               => 'partial',
//    'name'             => 'partial',
//    'author.firstName' => 'partial'
//])]
//#[ApiFilter(RangeFilter::class, properties: ['price'])]
//#[ApiFilter(DateFilter::class, properties: ['created_at' => DateFilter::EXCLUDE_NULL])]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info:book', 'info:basketItem', 'user:response', 'info-item:book'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Length(min: 5, groups: ['create:book'])]
    #[Groups(['create:book', 'info:book', 'update:book', 'info:basketItem', 'info-item:book'])]
    private ?string $name = null;

    #[ORM\Column(length: 1000)]
    #[Length(min: 5, max: 1000, maxMessage: "Description too long", groups: ['create:book', 'update:book'])]
    #[Groups(['create:book', 'info:book', 'update:book', 'info-item:book'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['create:book', 'info:book', 'update:book', 'info:basketItem', 'info-item:book'])]
    private int $price = 0;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'books')]
    #[Groups(['info-item:book'])]
    private ?User $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['info:book', 'info-item:book'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['update:book'])]
    private ?int $sale = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderItem::class)]
    private Collection $orderItems;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Comment::class)]
    #[Groups(['info-item:book', 'info:book'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: MediaFiles::class)]
    #[Groups(['info-item:book', 'info:book'])]
    private Collection $images;

    #[Vich\UploadableField(mapping: "media_object", fileNameProperty: "filePath")]
    #[ApiProperty(
        openapiContext: [
            'type'   => 'string',
            'format' => 'binary'
        ]
    )]
    #[Groups(['create:book'])]
    private ?UploadedFile $imageFile = null;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->orderItems = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
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

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBook($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBook() === $this) {
                $comment->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MediaFiles>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(MediaFiles $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setBook($this);
        }

        return $this;
    }

    public function removeImage(MediaFiles $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getBook() === $this) {
                $image->setBook(null);
            }
        }

        return $this;
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    public function setImageFile(?UploadedFile $imageFile): void
    {
        $this->imageFile = $imageFile;
    }
}
