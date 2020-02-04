<?php

namespace Uol\BoaCompra\Cron;

use Uol\BoaCompra\Helper\Transaction as HelperTransaction;
use Uol\BoaCompra\Logger\Logger;
use Uol\BoaCompra\Model\Http\Transaction as HttpTransaction;

class Transactions
{
    /**
     * @var HttpTransaction
     */
    private $httpTransaction;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var HelperTransaction
     */
    private $helperTransaction;

    /**
     * Transactions constructor.
     * @param HttpTransaction $httpTransaction
     * @param HelperTransaction $helperTransaction
     * @param Logger $logger
     */
    public function __construct(
        HttpTransaction $httpTransaction,
        HelperTransaction $helperTransaction,
        Logger $logger
    ) {
        $this->httpTransaction = $httpTransaction;
        $this->logger = $logger;
        $this->helperTransaction = $helperTransaction;
    }

    public function execute()
    {
        $initialDate = date('Y-m-d', strtotime('-20 day'));
        $finalDate = date('Y-m-d', strtotime('-1 day'));

        $result = $this->httpTransaction->create([
            'initial-order-date' => $initialDate . 'T00:00:00.000-03:00',
            'final-order-date' => $finalDate . 'T23:59:59.000-03:00'
        ]);

        $result = json_decode($result, true);
        if (!$transactions = $this->haveTransactions($result)) {
            return;
        }

        foreach ($transactions as $transaction) {
            $this->helperTransaction->updateStatus($transaction);
        }

        return;
    }

    /**
     * @param array $result
     * @return bool|array
     */
    private function haveTransactions($result = [])
    {
        if ($result['metadata']['found'] > 0) {
            return $result['transaction-result']['transactions'];
        }

        return false;
    }
}
