<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
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

    #[ORM\Column(length: 255)]
    private ?string $role = 'ROLE_USER';

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 255)]
    private ?string $verifiedToken = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deleteToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastVerificationEmailSentAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
        $this->verifiedToken = Uuid::v4()->toRfc4122();
        $this->deleteToken = Uuid::v4()->toRfc4122();
        $this->userId = Uuid::v4()->toRfc4122();
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
        return ['ROLE_USER'];
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

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

    public function setVerifiedToken(string $verifiedToken): static
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

    public function getLastVerificationEmailSentAt(): ?\DateTimeImmutable
    {
        return $this->lastVerificationEmailSentAt;
    }

    public function setLastVerificationEmailSentAt(?\DateTimeImmutable $lastVerificationEmailSentAt): static
    {
        $this->lastVerificationEmailSentAt = $lastVerificationEmailSentAt;

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
}
