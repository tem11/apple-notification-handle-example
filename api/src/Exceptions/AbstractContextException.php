<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Extend this abstract class with extensions to provide additional context for proper logging
 */
abstract class AbstractContextException extends Exception
{
    /**
     * AbstractContextException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $context
     */
    public function __construct(
        string $message = "",
        private array $context = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    final public function getContext(): array
    {
        return $this->context;
    }
}
