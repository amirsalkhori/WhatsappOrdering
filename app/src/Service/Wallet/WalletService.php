<?php

namespace App\Service\Wallet;

use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;

final class WalletService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createWallet($user)
    {
        $wallet = new Wallet();
        $wallet->setAfterAmount(0.0);
        $wallet->setBeforeAmount(0.0);
        $wallet->setEffectiveAmount(0.0);
        $wallet->setReason('Create wallet');
        $wallet->setStatus(1);
        $wallet->setOwner($user);
        $wallet->setCreatedAt(new \DateTime());
        $wallet->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($wallet);

        $this->entityManager->flush();

        return $wallet;
    }
}