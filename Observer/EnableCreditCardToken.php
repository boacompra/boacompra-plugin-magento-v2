<?php

namespace Uol\BoaCompra\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;
use Uol\BoaCompra\Model\Payment\CreditCard;

class EnableCreditCardToken implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Json
     */
    private $serialized;

    /**
     * EnableCreditCardToken constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param Json $serialized
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Json $serialized,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->serialized = $serialized;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getData('order');

        if ($order->getPayment()->getMethod() != CreditCard::CODE) {
            return;
        }

        if ($order->getCustomerIsGuest()) {
            return;
        }

        $transaction = $observer->getData('transaction');
        if (!array_key_exists('status', $transaction) || $transaction['status'] != 'COMPLETE') {
            return;
        }

        try {
            $customer = $this->customerRepository->getById($order->getCustomerId());
            if (!$customer->getCustomAttribute('boacompra_cc_token')) {
                return;
            }
            $customer->setCustomAttribute('boacompra_cc_token_is_active', true);
            $this->customerRepository->save($customer);
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }

        return;
    }
}
