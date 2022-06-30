<?php


namespace App\Service\DiscountUser;


use App\Entity\DiscountUser;
use Doctrine\ORM\EntityManagerInterface;

final class DiscountUserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createDiscountUser($user, $discount)
    {
        $discountUser = new DiscountUser();
        $discountUser->setUsers($user);
        $discountUser->setDiscount($discount);
        $discountUser->setCreatedAt(new \DateTime());
        $discountUser->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($discountUser);

        $this->entityManager->flush();

        return $discountUser;
    }
}