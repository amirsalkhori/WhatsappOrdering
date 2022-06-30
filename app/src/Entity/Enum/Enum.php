<?php

namespace App\Entity\Enum;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

Abstract class Enum
{
    Abstract static function getAvailableTypes();

    public static function checkIfValueIsValid($value)
    {
        if (!in_array($value, static::getAvailableTypes()))
        {
            throw new BadRequestException("Incorrect type $value value.", 400);
        }
    }
}
