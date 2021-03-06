<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    /** @TODO use php8 enum */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING_BILLING = 'pending';
    public const STATUS_CLOSED = 'closed';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subscriptionRef;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="subscription")
     */
    private $transactions;

    public function __construct(
        $subscriptionRef,
        $expiresAt,
        $status
    ) {
        $this->subscriptionRef = $subscriptionRef;
        $this->expiresAt = $expiresAt;
        $this->status = $status;

        $this->transactions = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function isActive(): bool
    {
        return $this->status !== self::STATUS_CLOSED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriptionRef(): ?string
    {
        return $this->subscriptionRef;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function setExpiresAt(DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setSubscription($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSubscription() === $this) {
                $transaction->setSubscription(null);
            }
        }

        return $this;
    }
}
