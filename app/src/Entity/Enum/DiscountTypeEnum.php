<?php


namespace App\Entity\Enum;


class DiscountTypeEnum extends Enum
{
    const CHARGE_CODE = 'chargeCode';
    const DISCOUNT_CODE = 'discountCode';

    static function getAvailableTypes()
    {
        return [
            self::CHARGE_CODE,
            self::DISCOUNT_CODE
        ];
    }
}
