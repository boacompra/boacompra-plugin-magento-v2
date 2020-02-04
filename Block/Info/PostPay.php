<?php

namespace Uol\BoaCompra\Block\Info;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Block\Info;
use Magento\Sales\Api\Data\OrderInterface;

class PostPay extends Info
{
    protected $_template = 'Uol_BoaCompra::info/postpay.phtml';

    public function isPending()
    {
        return (bool) ($this->getOrder()->getStatus() == 'pending');
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getPaymentUrl()
    {
        return $this->getOrder()->getPayment()->getAdditionalInformation('payment_url');
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getDigitableLine()
    {
        return $this->getOrder()->getPayment()->getAdditionalInformation('digitable_line');
    }


    /**
     * @return OrderInterface
     * @throws LocalizedException
     */
    public function getOrder()
    {
        return $this->getInfo()->getOrder();
    }
}
