<?php

namespace DataPlay\Services\Exceptions;

use Exception;
use Throwable;

class DataGeneratorException extends Exception implements DataPlayServiceException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $formattedMessage = "[DataGenerator] Error: {$message}";

        parent::__construct($formattedMessage, $code, $previous);
    }
}
