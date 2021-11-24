<?php

namespace App\Entity;

use App\Interfaces\NotificationStatus;
use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction implements NotificationStatus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $referenceId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subscriptionId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Subscription::class, inversedBy="OneToMany")
     * @ORM\JoinColumn(nullable=true)
     */
    private $subscription;

    private ?string $subscriptionReference;

    public function __construct(
        string $referenceId,
        string $subscriptionReference,
        string $status,
        string $provider,
        DateTimeImmutable $expiresAt,
    ) {
        $this->referenceId = $referenceId;
        $this->subscriptionReference = $subscriptionReference;
        $this->status = $status;
        $this->provider = $provider;
        $this->expiresAt = $expiresAt;

        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function getSubscriptionReference(): ?string
    {
        return $this->subscriptionReference;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

}
