<?php

namespace App\Controller\Discount;

use App\Service\Discount\DiscountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DiscountCheckController extends AbstractController
{
    private DiscountService $discount;

    public function __construct(DiscountService $discount)
    {
        $this->discount = $discount;
    }

    public function __invoke(Request $request)
    {
        $requestBody = json_decode($request->getContent(), true);

       return $this->discount->checkValidDiscount($requestBody);
    }
}