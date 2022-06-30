<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Bridge\CreatedAt;
use App\Bridge\UpdatedAt;
use App\Bridge\UserHashInterface;
use App\Controller\Users\OtpController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(collectionOperations: [
    'post'=> ["security" => "is_granted('ROLE_ADMIN')"],
    'get' => ["security" => "is_granted('ROLE_ADMIN')"],
    'otp' => [
        'method' => 'POST',
        'path' => '/users/otp',
        'controller' => OtpController::class,
        'openapi_context'=>[
            'requestBody'=>[
                'content'=>[
                    'application/ld+json'=>[
                        'schema'=>[
                            'type'=> 'object',
                            'properties'=>[
                                'phoneNumber'=>[
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
        'normalization_context' => ['groups' => ['user_read']],
        'denormalization_context' => ['groups' => ['user_write']],]
)
]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, UserHashInterface, CreatedAt, UpdatedAt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["user_read", "order_read"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    #[Groups(["user_read", "user_write", "order_read"])]
    private $phoneNumber;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\Column(type: 'json')]
    #[Groups(["user_read"])]
    private $roles = [];

    #[ORM\OneToMany(mappedBy: 'users', targetEntity: DiscountUser::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $discountUsers;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["user_read"])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["user_read"])]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Wallet::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $wallets;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Order::class)]
    private $orders;

    public function __construct()
    {
        $this->discountUsers = new ArrayCollection();
        $this->wallets = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
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
            $discountUser->setUsers($this);
        }

        return $this;
    }

    public function removeDiscountUser(DiscountUser $discountUser): self
    {
        if ($this->discountUsers->removeElement($discountUser)) {
            // set the owning side to null (unless already changed)
            if ($discountUser->getUsers() === $this) {
                $discountUser->setUsers(null);
            }
        }

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

    /**
     * @return Collection<int, Wallet>
     */
    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    public function addWallet(Wallet $wallet): self
    {
        if (!$this->wallets->contains($wallet)) {
            $this->wallets[] = $wallet;
            $wallet->setOwner($this);
        }

        return $this;
    }

    public function removeWallet(Wallet $wallet): self
    {
        if ($this->wallets->removeElement($wallet)) {
            // set the owning side to null (unless already changed)
            if ($wallet->getOwner() === $this) {
                $wallet->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setOwner($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getOwner() === $this) {
                $order->setOwner(null);
            }
        }

        return $this;
    }
}
