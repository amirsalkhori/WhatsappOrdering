<?php

namespace App\DataFixtures;

use App\Entity\Discount;
use App\Entity\DiscountUser;
use App\Entity\Enum\DiscountTypeEnum;
use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        //Create User admin
        $user = new User();
        $user->setEmail('arvan@arvan.com');
        $user->setPhoneNumber('9397326612');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'hero123456789'));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        // Create wallet for our user
        $wallet = new Wallet();
        $wallet->setAfterAmount(5000000);
        $wallet->setBeforeAmount(0.0);
        $wallet->setEffectiveAmount(5000000);
        $wallet->setReason('Use charge code');
        $wallet->setStatus(1);
        $wallet->setOwner($user);
        $wallet->setCreatedAt(new \DateTime());
        $wallet->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($wallet);

        $this->entityManager->flush();

        //Create discount
        $discount = new Discount();
        $discount->setName("Football charge");
        $discount->setStartTime(new \DateTime());
        $discount->setEndTime((new \DateTime())->add(new \DateInterval('PT90M')));
        $discount->setDiscountType(DiscountTypeEnum::CHARGE_CODE);
        $discount->setAmount(5000000);
        $discount->setLimite(1000);
        $discount->setCode('Arvan-football');
        $discount->setCreatedAt(new \DateTime());
        $discount->setUpdatedAt(new \DateTime());
        $discount->setStatus(true);
        $this->entityManager->persist($discount);

        $this->entityManager->flush();

        //create discountCode
        $discountUser = new DiscountUser();
        $discountUser->setUsers($user);
        $discountUser->setDiscount($discount);
        $discountUser->setCreatedAt(new \DateTime());
        $discountUser->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($discountUser);

        $this->entityManager->flush();
    }
}
