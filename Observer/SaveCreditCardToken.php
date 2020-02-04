<?php

namespace Uol\BoaCompra\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Psr\Log\LoggerInterface;
use Uol\BoaCompra\Enum\CcType;
use Uol\BoaCompra\Model\Payment\CreditCard;

class SaveCreditCardToken implements ObserverInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var Json
     */
    private $serialized;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveCreditCardToken constructor.
     * @param Session $checkoutSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Json $serialized
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $checkoutSession,
        CustomerRepositoryInterface $customerRepository,
        Json $serialized,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerRepository = $customerRepository;
        $this->serialized = $serialized;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $paymentMethod = $observer->getData('payment_method');
        if ($paymentMethod !== CreditCard::CODE) {
            return;
        }

        if ($this->checkoutSession->getQuote()->getCustomerIsGuest()) {
            return;
        }

        $transaction = $observer->getData('transaction');
        if (!array_key_exists('payment-methods', $transaction)) {
            return;
        }

        try {
            $creditCardToken = $transaction['payment-methods'][0];

            $ccData = $this->prepareData($creditCardToken);

            $customer = $this->checkoutSession->getQuote()->getCustomer();
            $customer->setCustomAttribute('boacompra_cc_token', $this->serialized->serialize($ccData));
            $customer->setCustomAttribute('boacompra_cc_token_is_active', false);
            $this->customerRepository->save($customer);
        } catch (InputException $e) {
        } catch (InputMismatchException $e) {
        } catch (LocalizedException $e) {
            $this->logger->debug($e->getMessage());
        }

        return;
    }

    /**
     * @param $creditCardToken
     * @return array
     */
    private function prepareData($creditCardToken)
    {
        list($year, $month) = explode('-', $creditCardToken['credit-card']['expiration-date']);
        $lastNumber = preg_replace("/[^0-9]/", "", $creditCardToken['credit-card']['masked-number']);

        return [
            'code' => $creditCardToken['code'],
            'cc-type' => CcType::getCode($creditCardToken['credit-card']['brand']),
            'brand' => $creditCardToken['credit-card']['brand'],
            'exp-month' => $month,
            'exp-year' => $year,
            'last-number' => $lastNumber,
            'date-created' => $creditCardToken['date-created']
        ];
    }
}
