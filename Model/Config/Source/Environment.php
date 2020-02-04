<?php

namespace Uol\BoaCompra\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Environment implements ArrayInterface
{
    const SANDBOX = 'sandbox';
    const PRODUCTION = 'production';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SANDBOX, 'label' => __('Sandbox')],
            ['value' => self::PRODUCTION, 'label' => __('Production')]
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::SANDBOX => __('Sandbox'),
            self::PRODUCTION => __('Production')
        ];
    }
}
