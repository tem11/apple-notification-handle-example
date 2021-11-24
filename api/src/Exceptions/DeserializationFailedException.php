<?php

namespace App\Exceptions;

use Throwable;

/**
 * Exception must be used only in API Payload deserialization process
 */
class DeserializationFailedException extends AbstractContextException
{
    public const HTTP_ERROR_CODE = 400;

    public function __construct(string $message = "", array $context = [], Throwable $previous = null)
    {
        parent::__construct($message, $context, self::HTTP_ERROR_CODE, $previous);
    }
}
