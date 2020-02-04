<?php

namespace Uol\BoaCompra\Controller\Notification;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Uol\BoaCompra\Model\Notification\Transaction;

class Index extends Action implements CsrfAwareActionInterface
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * Index constructor.
     * @param Context $context
     * @param Transaction $transaction
     */
    public function __construct(
        Context $context,
        Transaction $transaction
    ) {
        parent::__construct($context);
        $this->transaction = $transaction;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $this->transaction->execute($params);

        die('Done');
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
