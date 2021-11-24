<?php

namespace App\Controller\CallbackController;

use App\DTO\Notification\Apple\Notification;
use App\Exceptions\Notification\PaymentManagerNotFoundException;
use App\Exceptions\Transaction\CantHandleTransactionException;
use App\Exceptions\Transaction\DuplicateTransactionException;
use App\Manager\NotificationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppleCallbackController extends AbstractController
{
    public function __construct(
        private NotificationManager $notificationManager)
    {}

    #[Route('/api/callback/apple/v1', name: 'apple_v1_callback_handler')]
    public function handle(Notification $appleNotification): Response
    {
        try {
            if (false === $this->notificationManager->handle($appleNotification)) {
                return new JsonResponse([
                    'error' => 'Notification can`t be processed by the system'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return new Response(null);
        } catch (PaymentManagerNotFoundException) {
            return new JsonResponse(['error' => 'Can`t process provided entity. Requested callback provider is not supported.'], Response::HTTP_NOT_IMPLEMENTED);
        } catch (DuplicateTransactionException) {
            return new JsonResponse(['error' => 'Transaction already exists'], Response::HTTP_CONFLICT);
        } catch (CantHandleTransactionException) {
            return new JsonResponse(['error' => 'Appropriate notification handler not found'], Response::HTTP_NOT_IMPLEMENTED);
        }
    }
}