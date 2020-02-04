<?php

namespace Uol\BoaCompra\Block\Onepage;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;

class PostPay extends Template
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * PostPay constructor.
     * @param Template\Context $context
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->getOrder()->getPayment()->getAdditionalInformation('payment_url');
    }

    /**
     * @return string
     */
    public function getDigitableLine()
    {
        return $this->getOrder()->getPayment()->getAdditionalInformation('digitable_line');
    }

    /**
     * @return bool
     */
    public function isPostPay()
    {
        return (bool) ($this->getOrder()->getPayment()->getMethod() == \Uol\BoaCompra\Model\Payment\PostPay::CODE);
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->checkoutSession->getLastRealOrder();
    }
}
