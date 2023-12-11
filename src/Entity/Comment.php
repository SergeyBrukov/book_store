<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CommentController;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Service\Attribute\Required;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            routeName             : "create-comment-book",
            status                : JsonResponse::HTTP_CREATED,
            controller            : CommentController::class,
            normalizationContext  : ['groups' => ['info:comment']],
            denormalizationContext: ['groups' => ['create:comment']],
            security              : "is_granted('ROLE_USER')"
        )
    ]
)]
#[Patch(
    denormalizationContext: ['groups' => 'change:comment'],
    security              : "is_granted('ROLE_USER')"
)]
#[Delete(security: "is_granted('ROLE_USER')")]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info:comment', 'info:book'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['create:comment', 'info:comment', 'info-item:book', 'change:comment'])]
    #[Required]
    #[Length(min: 10, groups: ['create:comment'])]
    #[ApiProperty(
        openapiContext: [
            'example' => 'string'
        ]
    )]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['info-item:book', 'info:comment'])]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[Groups(['create:comment'])]
    #[ApiProperty(
        openapiContext: [
            'type'    => 'string',
            'format'  => 'string',
            'example' => 'id',
        ]
    )]
    private ?Book $book = null;

    #[ORM\Column]
    #[Groups(['create:comment', 'info:comment'])]
    private ?int $orderId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): static
    {
        $this->orderId = $orderId;

        return $this;
    }
}
