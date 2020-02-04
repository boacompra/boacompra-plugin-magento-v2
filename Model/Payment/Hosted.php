<?php

namespace Uol\BoaCompra\Model\Payment;

use Magento\Quote\Api\Data\CartInterface;

class Hosted extends AbstractMethod
{
    const CODE = 'boacompra_hosted';

    protected $_code = self::CODE;

    /**
     * @inheritDoc
     */
    public function isAvailable(CartInterface $quote = null)
    {
        if (!$this->helperData->isHostedType()) {
            return false;
        }

        return $this->helperData->isActive();
    }
}
