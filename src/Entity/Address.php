<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use App\Enum\AddressTypeEnum;
use App\Repository\AddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: AddressTypeEnum::class)]
    #[Assert\NotNull(message: 'Address type is required')]
    #[Assert\Count(
        min: 1,
        max: 2,
        minMessage: 'You must specify at least one address type',
        maxMessage: 'You can specify at most two address types'
    )]
    #[Assert\All([
        new Assert\Type(AddressTypeEnum::class),
        new Assert\Choice(
            callback: [AddressTypeEnum::class, 'cases'],
            multiple: true,
            message: 'Invalid address type. Valid values are: {{ choices }}'
        )
    ])]
    private array $addressType = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Street is required')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Street must be at least {{ limit }} characters long',
        maxMessage: 'Street cannot be longer than {{ limit }} characters'
    )]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Postal code is required')]
    #[Assert\Regex(
        pattern: '/^[0-9]{4,10}$/',
        message: 'Postal code must contain only numbers (4-10 digits)'
    )]
    private ?string $postal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'City is required')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'City must be at least {{ limit }} characters long',
        maxMessage: 'City cannot be longer than {{ limit }} characters'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s\-\.]+$/',
        message: 'City can only contain letters, spaces, dots and hyphens'
    )]
    private ?string $city = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'User is required')]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
        $this->updatedAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
        $this->addressType = [];
        $this->orders = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));

        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return AddressTypeEnum[]
     */
    public function getAddressType(): array
    {
        return $this->addressType;
    }

    public function setAddressType(array $addressType): static
    {
        // Ensure we have an array of AddressTypeEnum instances
        $this->addressType = array_map(function($type) {
            if ($type instanceof AddressTypeEnum) {
                return $type;
            }
            if (is_string($type)) {
                return AddressTypeEnum::tryFrom($type) ?? $type;
            }
            return $type;
        }, $addressType);

        return $this;
    }

    public function addAddressType(AddressTypeEnum $type): static
    {
        if (!in_array($type, $this->addressType, true)) {
            $this->addressType[] = $type;
        }

        return $this;
    }

    public function removeAddressType(AddressTypeEnum $type): static
    {
        if (($key = array_search($type, $this->addressType, true)) !== false) {
            unset($this->addressType[$key]);
            $this->addressType = array_values($this->addressType); // Reindex array
        }

        return $this;
    }

    public function hasAddressType(AddressTypeEnum $type): bool
    {
        return in_array($type, $this->addressType, true);
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = trim($street);

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(string $postal): static
    {
        $this->postal = preg_replace('/\s+/', '', $postal); // Remove spaces

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = trim($city);

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAddressTypeAsString(): string
    {
        return implode(', ', array_map(fn($type) => $type instanceof AddressTypeEnum ? $type->value : $type, $this->addressType));
    }
}