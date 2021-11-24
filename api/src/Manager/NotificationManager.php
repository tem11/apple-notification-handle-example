<?php

namespace App\Manager;

use App\Exceptions\Notification\PaymentManagerNotFoundException;
use App\Exceptions\Transaction\CantHandleTransactionException;
use App\Exceptions\Transaction\DuplicateTransactionException;
use App\Exceptions\Transaction\UpdateInactiveSubscriptionException;
use App\Factory\TransactionFactory;
use App\Interfaces\PaymentManager\PaymentManagerInterface;
use App\Interfaces\PaymentNotificationInterface;
use App\Manager\Payment\ApplePaymentManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class NotificationManager
{
    /**
     * @var PaymentManagerInterface[]
     */
    private array $paymentManagers;

    public function __construct(
        private TransactionFactory     $transactionManager,
        private SubscriptionManager    $subscriptionManager,
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger,
        // TODO Use compiler pass, group paymentManagers by tag and pass them in constructor as array
        ApplePaymentManager            $applePaymentManager,
    )
    {
        $this->paymentManagers[] = $applePaymentManager;
    }

    /**
     * This method MUST do transactional processing of the request to
     * either process transaction or not store it!
     *
     * @throws DuplicateTransactionException|PaymentManagerNotFoundException|UnauthorizedHttpException
     * @throws CantHandleTransactionException|UpdateInactiveSubscriptionException
     */
    public function handle(PaymentNotificationInterface $paymentNotification): bool
    {
        $paymentProvider = $this->getPaymentManager($paymentNotification);
        if (!$paymentProvider->verifySignature($paymentNotification->getSignature())) {
            throw new UnauthorizedHttpException('Signature can`t be verified');
        }

        $this->entityManager->beginTransaction();
        $transaction = $this->transactionManager->prepareTransaction($paymentNotification, $paymentProvider->getName());
        $this->entityManager->persist($transaction);
        $processingResult = $this->subscriptionManager->processTransaction($transaction);
        if ($processingResult === false) {
            $this->entityManager->rollback();
            $this->logger->warning('Can`t process transaction', [
                'transaction_ref' => $transaction->getReferenceId()
            ]);
            return false;
        }

        $this->entityManager->commit();
        $this->entityManager->flush();

        return true;
    }

    /**
     * @throws PaymentManagerNotFoundException
     */
    private function getPaymentManager(PaymentNotificationInterface $paymentNotification): PaymentManagerInterface
    {
        foreach ($this->paymentManagers as $paymentManager) {
            if ($paymentManager->supports($paymentNotification::class)) {
                return $paymentManager;
            }
        }

        throw new PaymentManagerNotFoundException();
    }
}