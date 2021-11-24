<?php

namespace App\Exceptions\Api;

use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * This exception must be used to raise validation exception
 * $violations param is required. If there is no violation, then this is incorrect exception to use.
 */
class UnprocessableEntityException extends RuntimeException
{
    public const HTTP_ERROR_CODE = 422;

    public function __construct(
        private ConstraintViolationListInterface $violations,
        string $message = "Unprocessable entity",
        Throwable $previous = null
    ) {
        parent::__construct($message, self::HTTP_ERROR_CODE, $previous);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

}
