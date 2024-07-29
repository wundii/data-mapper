<?php

declare(strict_types=1);

namespace Wundii\DataMapper\Exception;

use Exception;
use Throwable;

class DataMapperException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $throwable = null)
    {
        parent::__construct($message, $code, $throwable);
    }
}
