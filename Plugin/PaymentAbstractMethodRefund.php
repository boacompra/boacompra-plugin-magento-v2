<?php

namespace Uol\BoaCompra\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Uol\BoaCompra\Model\Http\Refund as HttpRefund;
use Uol\BoaCompra\Model\Payment\CreditCard;
use Uol\BoaCompra\Model\Payment\EWallet;
use Uol\BoaCompra\Model\Payment\Hosted;
use Uol\BoaCompra\Model\Payment\PostPay;
use Uol\BoaCompra\Model\Request\Refund as RequestRefund;

class PaymentAbstractMethodRefund
{
    const REFUND_ID = 'refund-id';

    /**
     * @var HttpRefund
     */
    private $httpRefund;
    /**
     * @var RequestRefund
     */
    private $refundRequestData;

    /**
     * Refund constructor.
     * @param RequestRefund $refundRequestData
     * @param HttpRefund $httpRefund
     */
    public function __construct(
        RequestRefund $refundRequestData,
        HttpRefund $httpRefund
    ) {

        $this->httpRefund = $httpRefund;
        $this->refundRequestData = $refundRequestData;
    }

    /**
     * @param AbstractMethod $subject
     * @param $result
     * @param InfoInterface $payment
     * @param $amount
     * @return mixed
     * @throws LocalizedException
     */
    public function afterRefund(AbstractMethod $subject, $result, InfoInterface $payment, $amount)
    {
        if (!in_array($payment->getMethod(), $this->availableMethods())) {
            return $result;
        }

        $refundRequestData = $this->refundRequestData->build($payment, $amount);

        if (!$result = $this->httpRefund->create($refundRequestData)) {
            throw new LocalizedException(__('Could not complete refund'));
        }

        $result = json_decode($result, true);

        if (array_key_exists(self::REFUND_ID, $result)) {
            $payment->setAdditionalInformation('refund_id', $result[self::REFUND_ID]);
            return $result;
        }

        throw new LocalizedException(__('Could not complete refund'));
    }

    /**
     * @return array
     */
    private function availableMethods()
    {
        return [
            Hosted::CODE,
            EWallet::CODE,
            PostPay::CODE,
            CreditCard::CODE
        ];
    }
}
