<?php

namespace App\Service\Order;

use App\Entity\Discount;
use App\Entity\Order;
use App\Entity\Wallet;
use App\Service\Discount\DiscountService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Security;

final class OrderService
{
    private EntityManagerInterface $entityManager;
    private DiscountService $discountService;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, DiscountService $discountService, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->discountService = $discountService;
        $this->security = $security;
    }

    public function createOrder($requestBody)
    {
        $finalAmount = 0.0;
        $user = $this->security->getUser();
        if (!array_key_exists("amount", $requestBody)) {
            throw new BadRequestException("Amount is required!", 400);
        }
        $amount = (float)$requestBody['amount'];
        if (array_key_exists("code", $requestBody)) {
            $code = trim($requestBody['code']);

            $amountDiscount = $this->discountService->checkValidDiscountCode($code, $amount, $user);
            $finalAmount = $amountDiscount;
            $this->calculateAmountOrder($amountDiscount, $user);
        }else{
            $this->calculateAmountOrder($amount, $user);
            $finalAmount = $amount;
        }

        $order = new Order();
        $order->setAmount($finalAmount);
        $order->setOwner($user);
        $this->entityManager->persist($order);

        $this->entityManager->flush();

        return $order;
    }

    public function calculateAmountOrder($total, $user)
    {


        $UserAmount = $this->entityManager->getRepository(Wallet::class)->findLastWallet($user->getId());
        if ($UserAmount[0]->getAfterAmount() < $total) {
            throw new BadRequestException('You have not enough amount', 400);
        } else {
            $wallet = new Wallet();
            $wallet->setOwner($user);
            $wallet->setCreatedAt(new \DateTime());
            $wallet->setUpdatedAt(new \DateTime());
            $wallet->setReason('Buy something');
            $wallet->setStatus(0);
            $wallet->setBeforeAmount($UserAmount[0]->getAfterAmount());
            $wallet->setAfterAmount($UserAmount[0]->getAfterAmount() - $total);
            $wallet->setEffectiveAmount($total);
            $this->entityManager->persist($wallet);

            $this->entityManager->flush();

            return $wallet;
        }
    }
}