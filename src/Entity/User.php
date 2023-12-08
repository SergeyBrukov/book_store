<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\AuthController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            routeName           : 'app_user_profile',
            status              : JsonResponse::HTTP_OK,
            controller          : AuthController::class,
            normalizationContext: ['groups' => ['user:profile', 'user:response']],
            security            : "is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')",
        ),
        new Post(
            routeName             : 'app_user_registration',
            status                : JsonResponse::HTTP_CREATED,
            controller            : AuthController::class,
            denormalizationContext: ['groups' => 'registration:user'],
            validationContext     : ['groups' => 'registration:user']
        ),
        new Post(
            routeName             : 'app_user_login',
            status                : JsonResponse::HTTP_OK,
            controller            : AuthController::class,
            normalizationContext  : ['groups' => 'login:user'],
            denormalizationContext: ['groups' => 'login:user'],
            validationContext     : ['groups' => 'login:user']
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    public const ROLE_ADMIN  = "ROLE_ADMIN";
    public const ROLE_CLIENT = "ROLE_CLIENT";


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['registration:user', 'info:order'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['registration:user', 'login:user', 'user:response', 'info:order', 'collection-info:order'])]
    #[Email(groups: ['registration:user'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:response'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['registration:user', 'login:user'])]
    #[Length(min: 5, max: 30, groups: ['registration:user'])]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    #[Length(min: 5, max: 30, groups: ['registration:user'])]
    #[Groups(['registration:user', 'user:response', 'info:order', 'collection-info:order'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 30)]
    #[Length(min: 5, max: 30, groups: ['registration:user'])]
    #[Groups(['registration:user', 'user:response', 'info:order', 'collection-info:order'])]
    private ?string $lastName = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:response'])]
    private ?Basket $basket = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Book::class)]
    #[Groups(['user:response'])]
    private Collection $books;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(Basket $basket): static
    {
        // set the owning side of the relation if necessary
        if ($basket->getUser() !== $this) {
            $basket->setUser($this);
        }

        $this->basket = $basket;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setOwner($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getOwner() === $this) {
                $order->setOwner(null);
            }
        }

        return $this;
    }
}
