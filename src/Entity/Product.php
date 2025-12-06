<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{

    private function generateSlug(string $title): stream_set_blocking
    {

        $slug = mb_strtolower($title, 'UTF-8');

        $search  = ['ä', 'ö', 'ü', 'ß', 'é', 'è', 'ê', 'à', 'á', 'â'];
        $replace = ['ae', 'oe', 'ue', 'ss', 'e', 'e', 'e', 'a', 'a', 'a'];
        $slug = str_replace($search, $replace, $slug);

        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);

        $slug = trim($slug, '-');

        if (empty($slug)) {
            $slug = 'product-' . uniqid();
        }
        
        return $slug;

    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug(): void
    {

        if($this->titel) {
            $this->slug = $this->generateSlug($this->titel);
        }

        $this->updatedAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "guid", unique: true)]
    private ?string $productId = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Title cannot be empty.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Title must be at least {{ limit }} characters long.',
        maxMessage: 'Title cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-.,!?()"\'&%$§]+$/u',
        message: 'Title contains invalid characters.'
    )]
    private ?string $titel = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Description cannot be empty.')]
    #[Assert\Length(
        min: 10,
        max: 5000,
        minMessage: 'Description must be at least {{ limit }} characters long.',
        maxMessage: 'Description cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-.,!?()"\'\n\r&%$§@+#*=]+$/u',
        message: 'Description contains invalid characters.'
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Price cannot be empty.')]
    #[Assert\PositiveOrZero(message: 'Price cannot be negative.')]
    #[Assert\Range(
        min: 0,
        max: 99999999.99,
        notInRangeMessage: 'Price must be between {{ min }} and {{ max }}.'
    )]
    #[Assert\Type(
        type: 'numeric',
        message: 'Price must be a valid number.'
    )]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Price must have up to 2 decimal places.'
    )]
    private ?string $price = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Stock cannot be empty.')]
    #[Assert\PositiveOrZero(message: 'Stock cannot be negative.')]
    #[Assert\Range(
        min: 0,
        max: 999999,
        notInRangeMessage: 'Stock must be between {{ min }} and {{ max }}.'
    )]
    #[Assert\Type(
        type: 'integer',
        message: 'Stock must be an integer.'
    )]
    private ?int $stock = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Active status must be specified.')]
    #[Assert\Type(
        type: 'bool',
        message: 'Active status must be true or false.'
    )]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?user $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->productId = Uuid::v4()->toRfc4122();
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
        $this->updatedAt = new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getTitel(): ?string
    {
        return $this->titel;
    }

    public function setTitel(string $titel): static
    {
        $this->titel = $titel;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedBy(): ?user
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?user $createdBy): static
    {
        $this->createdBy = $createdBy;

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
}
