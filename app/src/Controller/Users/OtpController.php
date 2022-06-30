<?php

namespace App\Controller\Users;

use App\Service\User\UserService;
use App\Service\Wallet\WalletService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OtpController extends AbstractController
{
    private UserService $userService;
    private WalletService $walletService;

    public function __construct(UserService $userService, WalletService $walletService)
    {
        $this->userService = $userService;
        $this->walletService = $walletService;
    }

    public function __invoke(Request $request)
    {
        $requestBody = json_decode($request->getContent(), true);
        if (!array_key_exists("phoneNumber", $requestBody)) {
            throw new BadRequestException("Phone number is required!", 400);
        }
        $phoneNumber = $this->userService->phoneValidation(trim($requestBody['phoneNumber']));
        $user = $this->userService->checkUserExist($phoneNumber);
        $userResponse = $this->userService->makeUserToken($user);

        return new JsonResponse($userResponse, 200);
    }
}