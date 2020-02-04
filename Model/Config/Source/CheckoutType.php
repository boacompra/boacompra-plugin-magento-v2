<?php

namespace Uol\BoaCompra\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CheckoutType implements ArrayInterface
{
    const HOSTED = 'hosted';
    const DIRECT_PAYMENT = 'direct_checkout';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::HOSTED, 'label' => __('Hosted')],
            ['value' => self::DIRECT_PAYMENT, 'label' => __('Direct Checkout')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::HOSTED => __('Hosted'),
            self::DIRECT_PAYMENT => __('Direct Checkout')
        ];
    }
}
