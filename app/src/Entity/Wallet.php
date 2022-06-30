<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Bridge\CreatedAt;
use App\Bridge\UpdatedAt;
use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(collectionOperations: [
    'get' => ["security" => "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')"],
    'post' => ["security" => "is_granted('ROLE_ADMIN')"],
],
    itemOperations: [
        'get' => ["security_post_denormalize" => "is_granted('WALLET_READ', object)"],
        'delete' => ["security" => "is_granted('ROLE_ADMIN')"],
        'put' => ["security" => "is_granted('ROLE_ADMIN')"],
    ],
    attributes: [
        'normalization_context' => ['groups' => ['wallet_read']],
        'denormalization_context' => ['groups' => ['wallet_write']],]
)
]
#[ApiFilter(SearchFilter::class, properties:
    [
        'owner.id' => 'exact',
    ])]
#[ApiFilter(OrderFilter::class)]
#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet implements CreatedAt, UpdatedAt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["wallet_read"])]
    private $id;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["wallet_read"])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["wallet_read"])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'wallets')]
    #[Groups(["wallet_read", "wallet_write"])]
    private $owner;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(["wallet_read", "wallet_write"])]
    private $beforeAmount = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(["wallet_read", "wallet_write"])]
    private $effectiveAmount = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(["wallet_read", "wallet_write"])]
    private $AfterAmount = 0.0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(["wallet_read", "wallet_write"])]
    private $reason;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(["wallet_read", "wallet_write"])]
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;

    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;

    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getBeforeAmount(): ?float
    {
        return $this->beforeAmount;
    }

    public function setBeforeAmount(?float $beforeAmount): self
    {
        $this->beforeAmount = $beforeAmount;

        return $this;
    }

    public function getEffectiveAmount(): ?float
    {
        return $this->effectiveAmount;
    }

    public function setEffectiveAmount(?float $effectiveAmount): self
    {
        $this->effectiveAmount = $effectiveAmount;

        return $this;
    }

    public function getAfterAmount(): ?float
    {
        return $this->AfterAmount;
    }

    public function setAfterAmount(?float $AfterAmount): self
    {
        $this->AfterAmount = $AfterAmount;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
