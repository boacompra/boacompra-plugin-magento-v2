<?php

namespace Uol\BoaCompra\Model\Request;

use Magento\Payment\Model\InfoInterface;
use Uol\BoaCompra\Helper\Data;

class Refund
{
    const TRANSACTION_ID = "transaction-id";
    const AMOUNT = "amount";
    const NOTIFY_URL = "notify-url";
    const TEST_MODE = "test-mode";
    const REFERENCE = "reference";

    /**
     * @var Data
     */
    private $helperData;

    /**
     * Refund constructor.
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param InfoInterface $payment
     * @param $amount
     * @return array
     */
    public function build(InfoInterface $payment, $amount)
    {
        $data = [
            self::TEST_MODE => (int) $this->helperData->isSandboxMode(),
            self::NOTIFY_URL => $this->helperData->getNotificationUrl(),
            self::TRANSACTION_ID => str_replace('-refund', '', $payment->getTransactionId()),
            self::REFERENCE => $payment->getOrder()->getIncrementId()
        ];

        if ($payment->getOrder()->getBaseTotalPaid() > $amount) {
            $data = array_merge($data, [
                self::AMOUNT => $amount,
            ]);
        }

        return $data;
    }
}
