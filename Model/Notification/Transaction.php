<?php

namespace Uol\BoaCompra\Model\Notification;

use Exception;
use Uol\BoaCompra\Helper\Transaction as HelperTransaction;
use Uol\BoaCompra\Model\Http\Transaction as HttpTransaction;

class Transaction
{
    /**
     * @var HttpTransaction
     */
    private $httpTransaction;
    /**
     * @var HelperTransaction
     */
    private $helperTransaction;

    /**
     * Transaction constructor.
     * @param HttpTransaction $httpTransaction
     * @param HelperTransaction $helperTransaction
     */
    public function __construct(
        HttpTransaction $httpTransaction,
        HelperTransaction $helperTransaction
    ) {
        $this->httpTransaction = $httpTransaction;
        $this->helperTransaction = $helperTransaction;
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function execute($data = [])
    {
        if (!$this->isValid($data)) {
            throw new Exception('Not valid parameter to notification');
        }

        if (!$result = $this->httpTransaction->create($data)) {
            throw new Exception('Empty notification result');
        }

        $result = json_decode($result, true);

        foreach ($result['transaction-result']['transactions'] as $transaction) {
            $this->helperTransaction->updateStatus($transaction);
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isValid(array $data)
    {
        if (!array_key_exists('transaction-code', $data)
            || empty($data['transaction-code'])
        ) {
            return false;
        }

        return true;
    }
}
