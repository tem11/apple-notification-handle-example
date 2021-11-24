<?php

namespace App\EventListener;

use App\Exceptions\Api\UnprocessableEntityException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\ConstraintViolation;

class ExceptionListener
{

    public function __construct(private LoggerInterface $logger) {}

    public function onKernelException(ExceptionEvent $event): ExceptionEvent
    {
        $exception = $event->getThrowable();
        if ($exception instanceof UnprocessableEntityException) {
            $violations = $exception->getViolations();
            $response = [
                'error' => 'Unprocessable entity',
                'violations' => []
            ];
            foreach ($violations as $violation) {
                /** @var ConstraintViolation $violation*/
                $response['violations'][] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage()
                ];
            }

            $response = new JsonResponse($response, Response::HTTP_UNPROCESSABLE_ENTITY);
            $event->setResponse($response);

            return $event;
        }

        if ($exception instanceof HttpExceptionInterface) {
            /** @var HttpExceptionInterface $exception */
            $response = new JsonResponse(['error' => $exception->getMessage()], $exception->getStatusCode());
        } else {
            $response = new JsonResponse(['error' => 'Unexpected error'], Response::HTTP_INTERNAL_SERVER_ERROR);

            $this->logger->error($exception->getMessage(), [
                'code' => $exception->getCode(),
                'trace' => $exception->getTraceAsString(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        }

        $event->setResponse($response);

        return $event;
    }
}
