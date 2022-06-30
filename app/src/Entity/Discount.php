<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Bridge\CreatedAt;
use App\Bridge\UpdatedAt;
use App\Controller\Discount\DiscountCheckController;
use App\Entity\Enum\DiscountTypeEnum;
use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Discount\DiscountReportController;


#[ApiResource(collectionOperations: [
    'get' => ["security" => "is_granted('ROLE_ADMIN')"],
    'post' => ["security" => "is_granted('ROLE_ADMIN')"],
    'discountCheck' => [
        'method' => 'POST',
        'path' => '/discounts/discount_check',
        'controller' => DiscountCheckController::class,
        'openapi_context' => [
            'requestBody' => [
                'content' => [
                    'application/ld+json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'code' => [
                                    "type" => "string"
                                ],
                                'phoneNumber' => [
                                    "type" => "string"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'discountReport' => [
        'method' => 'POST',
        'path' => '/discounts/report',
        "security" => "is_granted('ROLE_ADMIN')",
        'controller' => DiscountReportController::class,
        'openapi_context' => [
            'requestBody' => [
                'content' => [
                    'application/ld+json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'code' => [
                                    "type" => "string"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
],
    itemOperations: [
        'get' => ["security" => "is_granted('ROLE_ADMIN')"],
        'delete' => ["security" => "is_granted('ROLE_ADMIN')"],
        'put' => ["security" => "is_granted('ROLE_ADMIN')"],
    ],
    attributes: [
        'normalization_context' => ['groups' => ['discount_read']],
        'denormalization_context' => ['groups' => ['discount_write']],]
)
]
#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount implements CreatedAt, UpdatedAt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["discount_read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(["discount_read", "discount_write"])]
    private $name;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank]
    #[Groups(["discount_read", "discount_write"])]
    private $startTime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank]
    #[Groups(["discount_read", "discount_write"])]
    private $endTime;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups(["discount_read", "discount_write"])]
    private $discountType;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    #[Assert\NotBlank]
    #[Groups(["discount_read", "discount_write"])]
    private $code;

    #[ORM\OneToMany(mappedBy: 'discount', targetEntity: DiscountUser::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $discountUsers;

    #[ORM\Column(type: 'float', length: 255)]
    #[Assert\NotBlank]
    #[Groups(["discount_read", "discount_write"])]
    private $amount = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["discount_read"])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["discount_read"])]
    private $updatedAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(["discount_read", "discount_write"])]
    private $limite;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(["discount_read", "discount_write"])]
    private $status = true;

    public function __construct()
    {
        $this->discountUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDiscountType(): ?string
    {
        return $this->discountType;
    }

    public function setDiscountType(?string $discountType): self
    {
        DiscountTypeEnum::checkIfValueIsValid($discountType);
        $this->discountType = $discountType;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, DiscountUser>
     */
    public function getDiscountUsers(): Collection
    {
        return $this->discountUsers;
    }

    public function addDiscountUser(DiscountUser $discountUser): self
    {
        if (!$this->discountUsers->contains($discountUser)) {
            $this->discountUsers[] = $discountUser;
            $discountUser->setDiscount($this);
        }

        return $this;
    }

    public function removeDiscountUser(DiscountUser $discountUser): self
    {
        if ($this->discountUsers->removeElement($discountUser)) {
            // set the owning side to null (unless already changed)
            if ($discountUser->getDiscount() === $this) {
                $discountUser->setDiscount(null);
            }
        }

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function getLimite(): ?int
    {
        return $this->limite;
    }

    public function setLimite(int $limite): self
    {
        $this->limite = $limite;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
}
