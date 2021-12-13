<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CurrencyTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return float
     */
    public function transform($value): float
    {
        return $value / 100;
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    public function reverseTransform($value): int
    {
        $currency = floatval($value);
        $currency *= 100.0;

        return intval($currency);
    }
}