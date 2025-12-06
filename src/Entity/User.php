<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "guid", unique: true)]
    private ?string $userId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'firstname cannot be empty.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'firstname must be at least {{ limit }} characters long.',
        maxMessage: 'firstname cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L} ]+$/u',
        message: 'firstname can only contain letters (no special characters or numbers).'
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'lastname cannot be empty.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'lastname must be at least {{ limit }} characters long.',
        maxMessage: 'lastname cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L} ]+$/u',
        message: 'lastname can only contain letters (no special characters or numbers).'
    )]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: 'Email cannot be empty.')]
    #[Assert\Email(message: 'Please enter a valid email address.')]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $verifiedToken = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $deleteToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'createdBy')]
    private Collection $products;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
        $this->verifiedToken = Uuid::v4()->toRfc4122();
        $this->userId = Uuid::v4()->toRfc4122();
        $this->roles = ['ROLE_USER'];
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        
        $this->roles = array_unique($roles);
        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        
        return $this;
    }

    public function removeRole(string $role): static
    {

        if ($role === 'ROLE_USER') {
            return $this;
        }
        
        $key = array_search($role, $this->roles, true);
        if ($key !== false) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
        
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getVerifiedToken(): ?string
    {
        return $this->verifiedToken;
    }

    public function setVerifiedToken(?string $verifiedToken): static
    {
        $this->verifiedToken = $verifiedToken;
        
        return $this;
    }

    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(\DateTimeImmutable $verifiedAt): static
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(\DateTimeImmutable $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getDeleteToken(): ?string
    {
        return $this->deleteToken;
    }

    public function setDeleteToken(?string $deleteToken): static
    {
        $this->deleteToken = $deleteToken;

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): static
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCreatedBy($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCreatedBy() === $this) {
                $product->setCreatedBy(null);
            }
        }

        return $this;
    }
}