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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriptionRef(): ?string
    {
        return $this->subscriptionRef;
    }

    public function setSubscriptionRef(string $subscriptionRef): self
    {
        $this->subscriptionRef = $subscriptionRef;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeImmutable $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $oneToMany): self
    {
        if (!$this->transactions->contains($oneToMany)) {
            $this->transactions[] = $oneToMany;
            $oneToMany->setSubscription($this);
        }

        return $this;
    }

    public function removeOneToMany(Transaction $oneToMany): self
    {
        if ($this->transactions->removeElement($oneToMany)) {
            // set the owning side to null (unless already changed)
            if ($oneToMany->getSubscription() === $this) {
                $oneToMany->setSubscription(null);
            }
        }

        return $this;
    }
}
