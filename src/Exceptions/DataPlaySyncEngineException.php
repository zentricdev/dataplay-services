<?php

namespace DataPlay\Services\Exceptions;

use Exception;
use Throwable;

class DataPlaySyncEngineException extends Exception implements DataPlayServiceException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $formattedMessage = "[HashEngine] Error: {$message}";

        parent::__construct($formattedMessage, $code, $previous);
    }
}
