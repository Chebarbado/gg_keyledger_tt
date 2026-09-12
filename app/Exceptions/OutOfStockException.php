<?php

namespace App\Exceptions;

use Exception;

class OutOfStockException extends Exception
{
    public function __construct(string $message = 'Товар только что раскупили')
    {
        parent::__construct($message);
    }
}
