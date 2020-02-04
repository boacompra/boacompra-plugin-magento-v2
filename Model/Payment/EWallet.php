<?php

namespace Uol\BoaCompra\Model\Payment;

use Magento\Quote\Api\Data\CartInterface;

class EWallet extends AbstractMethod
{
    const CODE = 'boacompra_ewallet';

    protected $_code = self::CODE;

    /**
     * @inheritDoc
     */
    public function isAvailable(CartInterface $quote = null)
    {
        if (!$this->helperData->isDirectCheckoutType()) {
            return false;
        }

        if (!$this->isAvailableDirectCheckoutType()) {
            return false;
        }

        return $this->helperData->isActive();
    }
}
