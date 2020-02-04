<?php

namespace Uol\BoaCompra\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Uol\BoaCompra\Helper\Data;

class BoaCompraConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * EWalletConfigProvider constructor.
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'boacompra' => [
                    'debug' => $this->helperData->enableLog()
                ]
            ]
        ];
    }
}
