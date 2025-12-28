<?php

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    private ?OrderStatus $orderStatus = null;

    #[ORM\Column]
    private array $DeliveryAddress = [];

    #[ORM\Column]
    private array $BillingAddress = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isFinished = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiresAt = null;

    /**
     * @var Collection<int, OrderItems>
     */
    #[ORM\OneToMany(targetEntity: OrderItems::class, mappedBy: 'orders')]
    private Collection $orderItems;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'order')]
    private Collection $payments;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(OrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    public function getDeliveryAddress(): array
    {
        return $this->DeliveryAddress;
    }

    public function setDeliveryAddress(array $DeliveryAddress): static
    {
        $this->DeliveryAddress = $DeliveryAddress;

        return $this;
    }

    public function getBillingAddress(): array
    {
        return $this->BillingAddress;
    }

    public function setBillingAddress(array $BillingAddress): static
    {
        $this->BillingAddress = $BillingAddress;

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

    public function isFinished(): ?bool
    {
        return $this->isFinished;
    }

    public function setIsFinished(bool $isFinished): static
    {
        $this->isFinished = $isFinished;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * @return Collection<int, OrderItems>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItems $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrders($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItems $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrders() === $this) {
                $orderItem->setOrders(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setOrder($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getOrder() === $this) {
                $payment->setOrder(null);
            }
        }

        return $this;
    }
}
