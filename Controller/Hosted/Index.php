<?php

namespace Uol\BoaCompra\Controller\Hosted;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Uol\BoaCompra\Helper\Hosted\RequestAPayment;
use Uol\BoaCompra\Logger\Logger;
use Uol\BoaCompra\Model\Request\Authorization\Hosted as HostedRequestData;

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
     * @var HostedRequestData
     */
    private $requestData;
    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderManagementInterface $orderManagement
     * @param HostedRequestData $requestData
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        OrderManagementInterface $orderManagement,
        HostedRequestData $requestData,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->requestData = $requestData;
        $this->orderManagement = $orderManagement;
        $this->logger = $logger;
    }

    public function execute()
    {
        $order = null;

        try {
            $order = $this->loadOrder();
            $requestData = $this->requestData->build($order);

            $this->logger->debug(__(
                'HOSTED - Submit payment request for order #%1 by customer #%2',
                $order->getIncrementId(),
                $order->getCustomerId()
                ),
                $requestData
            );

            $requestAPayment = new RequestAPayment($requestData);
            $requestAPayment->execute();

            return;
        } catch (Exception $e) {
            $this->logger->debug(
                __('HOSTED - Submit error for order #%1', $order->getIncrementId()),
                [
                    'exception' => $e->getMessage()
                ]
            );
        }

        $this->orderManagement->cancel($order->getEntityId());

        $this->_redirect('boacompra/error/index');
    }

    /**
     * @return OrderInterface
     * @throws Exception
     */
    private function loadOrder()
    {
        if (!$order = $this->checkoutSession->getLastRealOrder()->getId()) {
            throw new Exception(__('There is no order associated with this session.'));
        }

        return $this->orderRepository->get($order);
    }
}
