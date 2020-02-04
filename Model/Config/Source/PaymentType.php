<?php

namespace Uol\BoaCompra\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentType implements ArrayInterface
{
    const CREDIT_CARD = 'boacompra_creditcard';
    const POST_PAY = 'boacompra_postpay';
    const E_WALLET = 'boacompra_ewallet';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CREDIT_CARD, 'label' => __('Credit Card')],
            ['value' => self::POST_PAY, 'label' => __('PostPay (Boleto)')],
            ['value' => self::E_WALLET, 'label' => __('e-Wallet')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::CREDIT_CARD => __('Credit Card'),
            self::POST_PAY => __('PostPay (Boleto)'),
            self::E_WALLET => __('e-Wallet')
        ];
    }
}
