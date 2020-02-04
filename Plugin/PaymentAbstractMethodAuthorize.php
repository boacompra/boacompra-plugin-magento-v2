<?php

namespace Uol\BoaCompra\Plugin;

use Exception;
use Magento\Framework\Event\Manager;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Uol\BoaCompra\Model\Payment\CreditCard;
use Uol\BoaCompra\Model\Payment\EWallet;
use Uol\BoaCompra\Model\Payment\PostPay;
use Uol\BoaCompra\Model\Http\DirectCheckout as HttpDirectCheckout;
use Uol\BoaCompra\Model\Request\Authorization\Direct as DirectRequestData;

class PaymentAbstractMethodAuthorize
{
    /**
     * @var HttpDirectCheckout
     */
    private $httpDirectCheckout;
    /**
     * @var DirectRequestData
     */
    private $requestData;
    /**
     * @var Manager
     */
    private $eventManager;

    /**
     * Authorize constructor.
     * @param DirectRequestData $requestData
     * @param HttpDirectCheckout $httpDirectCheckout
     * @param Manager $eventManager
     */
    public function __construct(
        DirectRequestData $requestData,
        HttpDirectCheckout $httpDirectCheckout,
        Manager $eventManager
    ) {
        $this->requestData = $requestData;
        $this->httpDirectCheckout = $httpDirectCheckout;
        $this->eventManager = $eventManager;
    }

    /**
     * @param AbstractMethod $subject
     * @param $result
     * @param InfoInterface $payment
     * @param $amount
     * @return mixed
     * @throws Exception
     */
    public function afterAuthorize(AbstractMethod $subject, $result, InfoInterface $payment, $amount)
    {
        if (!in_array($subject->getCode(), $this->availableMethods())) {
            return $result;
        }

        $requestData = $this->requestData->build($payment);

        if (!$result = $this->httpDirectCheckout->create($requestData)) {
            throw new Exception(__('There has been a payment confirmation error. Verify data and try again'));
        }

        $result = json_decode($result, true);

        if (in_array($subject->getCode(), [PostPay::CODE, EWallet::CODE])) {
            $payment->setAdditionalInformation('payment_url', $result['transaction']['payment-url']);
        }

        if ($subject->getCode() == PostPay::CODE) {
            $payment->setAdditionalInformation('barcode_number', $result['transaction']['barcode-number']);
            $payment->setAdditionalInformation('digitable_line', $result['transaction']['digitable-line']);
        }

        $payment->setTransactionId($result['transaction']['code']);
        $payment->setParentTransactionId(null);
        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);

        $this->eventManager->dispatch('uol_boacompra_authorize_after', [
            'payment_method' => $subject->getCode(),
            'payment' => $payment,
            'transaction' => $result
        ]);

        return $result;
    }

    /**
     * @return array
     */
    private function availableMethods()
    {
        return [
            EWallet::CODE,
            PostPay::CODE,
            CreditCard::CODE
        ];
    }
}

