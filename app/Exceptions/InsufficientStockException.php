<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class InsufficientStockException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
