<?php

namespace Uol\BoaCompra\Model\Payment;

use Magento\Quote\Api\Data\CartInterface;
use Uol\BoaCompra\Block\Info\PostPay as InfoBlock;

class PostPay extends AbstractMethod
{
    const CODE = 'boacompra_postpay';

    protected $_code = self::CODE;
    protected $_infoBlockType = InfoBlock::class;

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
