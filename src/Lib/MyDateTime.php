<?php

namespace App\Lib;

/**
 * The only purpose of this class is to replace it under testing with a mock.
 */
class MyDateTime
{
    /**
     * @return \DateTime
     */
    public function getToday(): \DateTime
    {
        return new \DateTime();
    }
}
