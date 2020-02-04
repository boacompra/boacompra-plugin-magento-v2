<?php

namespace Uol\BoaCompra\Controller\Success;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        if (!$order = $this->checkoutSession->getLastRealOrder()) {
            return;
        }

        $this->_redirect('checkout/onepage/success');
    }
}
