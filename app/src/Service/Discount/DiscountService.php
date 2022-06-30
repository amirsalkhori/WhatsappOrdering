<?php

namespace App\Service\Discount;

use App\Entity\Discount;
use App\Entity\DiscountUser;
use App\Entity\User;
use App\Entity\Wallet;
use App\Service\DiscountUser\DiscountUserService;
use App\Service\User\UserService;
use App\Service\Wallet\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

final class DiscountService
{
    private EntityManagerInterface $entityManager;
    private UserService $userService;
    private WalletService $walletService;
    private DiscountUserService $discountUserService;

    public function __construct(EntityManagerInterface $entityManager, UserService $userService,
                                WalletService $walletService, DiscountUserService $discountUserService)
    {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
        $this->walletService = $walletService;
        $this->discountUserService = $discountUserService;
    }

    public function checkValidDiscount($requestBody)
    {
        if (!array_key_exists("phoneNumber", $requestBody)) {
            throw new BadRequestException("Phone number is required!", 400);
        }
        if (!array_key_exists("code", $requestBody)) {
            throw new BadRequestException("Code is required!", 400);
        }
        $phoneNumber = trim($requestBody['phoneNumber']);
        $code = trim($requestBody['code']);

        //Validation for phoneNumber
        $userPhoneNumber = $this->userService->phoneValidation($phoneNumber);

        //Check discount is valid
        /**
         * @var Discount $discount
         */
        $discount = $this->entityManager->getRepository(Discount::class)->checkValidDiscount($code);
        if (!$discount) {
            throw new BadRequestException('Invalid this code!', 400);
        }
        //Check user exist
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phoneNumber' => $userPhoneNumber]);
        if (!$user) {
            // Add new user and Create wallet
            $user = $this->userService->createUser($userPhoneNumber);
            // Add wallet for user
            $this->walletService->createWallet($user);
        }
        //Check discount limitation
        $countDiscountUser = $this->entityManager->getRepository(DiscountUser::class)->findBy(
            ['discount' => $discount->getId()]);
        if (count($countDiscountUser) > $discount->getLimite()) {
            throw new BadRequestException('This code has expired !', 400);
        }
        //Check discount user
        $discountUser = $this->entityManager->getRepository(DiscountUser::class)->findOneBy(
            ['users' => $user->getId(), 'discount' => $discount->getId()]);
        if ($discountUser) {
            throw new BadRequestException('You have used this code before !', 400);
        } else {
            //Add user discount
            $this->discountUserService->createDiscountUser($user, $discount);

            //Modify user wallet
            $userWallet = $this->entityManager->getRepository(Wallet::class)->findLastWallet($user->getId());
            $wallet = new Wallet();
            $wallet->setBeforeAmount($userWallet[0]->getAfterAmount());
            $wallet->setAfterAmount($userWallet[0]->getAfterAmount() + $discount->getAmount());
            $wallet->setEffectiveAmount($discount->getAmount());
            $wallet->setReason('Use charge code');
            $wallet->setStatus(1);
            $wallet->setOwner($user);
            $wallet->setCreatedAt(new \DateTime());
            $wallet->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($wallet);

            $this->entityManager->flush();

            $userResponse = $this->userService->makeUserToken($user);
            return new JsonResponse($userResponse);
        }
    }

    public function userUseDiscount($requestBody)
    {
        if (!array_key_exists("code", $requestBody)) {
            throw new BadRequestException("code is required!", 400);
        }
        $code = trim($requestBody['code']);
        $discountCode = $this->entityManager->getRepository(Discount::class)->findOneBy(['code' => $code]);
        if (!$discountCode)
            throw new BadRequestException('Invalid this code!', 400);

        $UserUseDiscount = $this->entityManager->getRepository(User::class)->findUserGiveCharge($code);

        return $UserUseDiscount;
    }

    public function checkValidDiscountCode($code, $mount, $user)
    {
        $totalAmountDiscount = 0.0;
        $discountAmount = 0.0;
        $checkValidDiscount = $this->entityManager->getRepository(Discount::class)->findValidDiscount($code);
        if (!$checkValidDiscount){
            throw new BadRequestException('This code is not valid!', 400);
        }
        //Check use discount code
        $discountUser = $this->entityManager->getRepository(DiscountUser::class)->findOneBy(
            ['users' => $user->getId(), 'discount' => $checkValidDiscount->getId()]);
        if ($discountUser)
            throw new BadRequestException('You have used this code before !', 400);
        //check discount limitation
        $countDiscountUser = $this->entityManager->getRepository(DiscountUser::class)->findBy(
            ['discount' => $checkValidDiscount->getId()]);
        if (count($countDiscountUser) > $checkValidDiscount->getLimite()) {
            throw new BadRequestException('This code has expired !', 400);
        }
        //Add discountUser
        $this->discountUserService->createDiscountUser($user, $checkValidDiscount);

        $discountAmount = $checkValidDiscount->getAmount();
        $totalAmountDiscount = (($mount * $discountAmount) / 100);

        return round(($mount - $totalAmountDiscount), 2);
    }
}