<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Bridge\CreatedAt;
use App\Bridge\UpdatedAt;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\Order\OrderController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(collectionOperations: [
    'post'=> ["security" => "is_granted('ROLE_ADMIN')"],
    'get' => ["security" => "is_granted('ROLE_ADMIN')"],
    'order' => [
        'method' => 'POST',
        'path' => '/orders/wowcher',
        'controller' => OrderController::class,
        "security" => "is_granted('ROLE_USER')",
        'openapi_context'=>[
            'requestBody'=>[
                'content'=>[
                    'application/ld+json'=>[
                        'schema'=>[
                            'type'=> 'object',
                            'properties'=>[
                                'amount'=>[
                                    "type" => 'float'
                                ],
                                'code'=>[
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
        'get'=> ["security_post_denormalize" => "is_granted('USER_READ', object)"],
        'delete'=> ["security" => "is_granted('ROLE_ADMIN')"],
        'put'=> ["security_post_denormalize" => "is_granted('USER_UPDATE', object)"]
    ],
    attributes: [
        'normalization_context' => ['groups' => ['order_read']],
        'denormalization_context' => ['groups' => ['order_write']],]
)
]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order implements CreatedAt, UpdatedAt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["order_read"])]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[Groups(["order_read", "order_write"])]
    private $owner;

    #[ORM\Column(type: 'float')]
    #[Groups(["order_read", "order_write"])]
    private $amount;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["order_read"])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["order_read"])]
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
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
}
