<?php

namespace Uol\BoaCompra\Plugin;

use Magento\Sales\Model\Order\Payment;
use Uol\BoaCompra\Model\Payment\CreditCard;
use Uol\BoaCompra\Model\Payment\EWallet;
use Uol\BoaCompra\Model\Payment\Hosted;
use Uol\BoaCompra\Model\Payment\PostPay;

class SalesOrderPaymentPlace
{
    /**
     * @param Payment $subject
     * @param $result
     * @return mixed
     */
    public function afterPlace(Payment $subject, $result)
    {
        if (in_array($subject->getMethod(), $this->paymentMethods())) {
            $order = $subject->getOrder();
            $order->setState('new')
                ->setStatus('pending');
        }

        return $result;
    }

    /**
     * @return array
     */
    private function paymentMethods()
    {
        return [
            Hosted::CODE,
            CreditCard::CODE,
            EWallet::CODE,
            PostPay::CODE
        ];
    }
}
