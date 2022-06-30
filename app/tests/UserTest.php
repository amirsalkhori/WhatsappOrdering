<?php

namespace Test;

use App\Entity\Discount;
use App\Entity\Enum\DiscountTypeEnum;
use PHPUnit\Framework\TestCase;


class UserTest extends TestCase
{
    private $model;

    /**
     * @before
     */
    public function setUp(): void
    {
        $discount = new Discount();
        $discount->setName("Football charge");
        $discount->setStartTime(new \DateTime());
        $discount->setEndTime((new \DateTime())->add(new \DateInterval('PT90M')));
        $discount->setDiscountType(DiscountTypeEnum::DISCOUNT_CODE);
        $discount->setAmount(5000000);
        $discount->setLimite(1000);
        $discount->setCode('Arvan-football');
        $discount->setCreatedAt(new \DateTime());
        $discount->setUpdatedAt(new \DateTime());
        $discount->setStatus(true);

        $this->model = $discount;
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Discount::class, $this->model);
    }

    public function testObject()
    {
        $this->assertEquals("Arvan-football", $this->model->getCode());
        $this->assertEquals(5000000, $this->model->getAmount());
        $this->assertEquals(1000, $this->model->getLimite());
        $this->assertEquals('discountCode', $this->model->getDiscountType());
    }
}