<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\Wallet\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserService
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;
    private JWTTokenManagerInterface $JWTTokenManager;
    private WalletService $walletService;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,
                                JWTTokenManagerInterface $JWTTokenManager,  WalletService $walletService)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->walletService = $walletService;
    }

    public function createUser($phoneNumber)
    {
        $user = new User();
        $user->setPhoneNumber($phoneNumber);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'hero' . $phoneNumber));
        $user->setEmail($phoneNumber . '@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($user);

        $this->entityManager->flush();

        return $user;
    }

    public function phoneValidation($phone)
    {
        $pattern = '/^(?:98|\+98|0098|0)?9[0-9]{9}$/';
        if (!preg_match($pattern, $phone)) {
            throw new BadRequestException("Invalid phoneNumber !", 400);
        }
        $userPhoneNumber = substr($phone, -10);

        return $userPhoneNumber;
    }

    public function makeUserToken($user)
    {
        $token = $this->JWTTokenManager->create($user);

        return [
            'id' => $user->getId(),
            'phoneNumber' => $user->getPhoneNumber(),
            'roles' => $user->getRoles(),
            'token' => $token,
        ];
    }

    public function checkUserExist($phoneNumber)
    {
        $userRepo = $this->entityManager->getRepository(User::class)->findOneBy(['phoneNumber' => $phoneNumber]);
        if (!$userRepo){
            $user = $this->createUser($phoneNumber);
            $this->walletService->createWallet($user);
            return $user;
        }
        else
            return $userRepo;
    }
}