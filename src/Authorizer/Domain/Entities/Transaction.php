<?php

namespace Nubank\Authorizer\Domain\Entities;

use DateTime;

class Transaction
{
    public function __construct(private string $merchant, private int $amount, private DateTime $time)
    {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getTime(): DateTime
    {
        return $this->time;
    }
}
