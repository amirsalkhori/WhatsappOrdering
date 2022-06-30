<?php

namespace App\Controller\Order;

use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function __invoke(Request $request)
    {
        $requestBody = json_decode($request->getContent(), true);

        return $this->orderService->createOrder($requestBody);
    }
}