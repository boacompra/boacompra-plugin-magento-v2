<?php

namespace Uol\BoaCompra\Controller\EWallet;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;

class Index extends Action
{
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute()
    {
        if (!$order = $this->checkoutSession->getLastRealOrder()->getId()) {
            throw new Exception(__('There is no order associated with this session.'));
        }

        $order = $this->orderRepository->get($order);
        $payment = $order->getPayment();

        return $this->resultRedirectFactory->create()->setUrl(
            $payment->getAdditionalInformation('payment_url')
        );
    }
}
