<?php

namespace Uol\BoaCompra\Helper;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Event\Manager;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Uol\BoaCompra\Enum\TransactionStatus;
use Uol\BoaCompra\Logger\Logger;

class Transaction
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var TransactionFactory
     */
    private $transactionFactory;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Manager
     */
    private $eventManager;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param InvoiceService $invoiceService
     * @param TransactionFactory $transactionFactory
     * @param Manager $eventManager
     * @param Logger $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        InvoiceService $invoiceService,
        TransactionFactory $transactionFactory,
        Manager $eventManager,
        Logger $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
    }

    /**
     * @param $transaction
     */
    public function updateStatus($transaction)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $transaction['order-id'], 'eq')
            ->create();

        $orderList = $this->orderRepository->getList($searchCriteria)->getItems();
        $order = reset($orderList);

        if (!$order->getEntityId()) {
            return;
        }

        if (in_array($order->getStatus(), ['canceled', 'closed'])) {
            return;
        }

        switch ($transaction['status']) {
            case TransactionStatus::EXPIRED:
            case TransactionStatus::NOT_PAID:
            case TransactionStatus::CANCELLED:
                $this->cancelOrder($order);
                break;
            case TransactionStatus::COMPLETE:
                $this->createInvoice($order, $transaction);
                break;
        }

        return;
    }

    /**
     * @param OrderInterface $order
     */
    private function cancelOrder(OrderInterface $order)
    {
        $order->setState(Order::STATE_CANCELED);
        $order->setStatus(Order::STATE_CANCELED);
        $order->addCommentToStatusHistory(__('Transaction was cancelled by UOL BoaCompra'));
        $order->save();

        $this->logger->debug(__('NOTIFICATION - Order %1 was cancelled by UOL BoaCompra', $order->getIncrementId()));
    }

    /**
     * @param OrderInterface $order
     * @param array $transaction
     */
    private function createInvoice(OrderInterface $order, $transaction = [])
    {
        $this->logger->debug(__('NOTIFICATION - Create Invoice for the order %1.', $order->getIncrementId()));

        if (!$order->canInvoice()) {
            $this->logger->debug(
                __('NOTIFICATION - Impossible to generate invoice for order %1.', $order->getIncrementId())
            );
            return;
        }

        try {
            $order->getPayment()->setTransactionId($transaction['transaction-code']);

            $invoice = $this->invoiceService->prepareInvoice($order);

            $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $invoice->setTransactionId($transaction['transaction-code']);
            $invoice->getOrder()->setCustomerNoteNotify(false);
            $invoice->getOrder()->setIsInProcess(true);

            $order->addStatusHistoryComment(
                __('Transaction was paid and approved. Products should be deliver to the End User'),
                false
            );

            $transactionSave = $this->transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
            ;
            $transactionSave->save();
        } catch (Exception $e) {
            $this->logger->debug(__('NOTIFICATION - Error creating invoice: '. $e->getMessage()));
            return;
        }

        $this->eventManager->dispatch('uol_boacompra_transaction_invoice_after', [
            'transaction' => $transaction,
            'order' => $order
        ]);

        $this->logger->debug(__('NOTIFICATION - Invoice created with success'));
        return;
    }
}
