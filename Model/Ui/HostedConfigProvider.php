<?php

namespace Uol\BoaCompra\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Payment\Hosted;

class HostedConfigProvider implements ConfigProviderInterface
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
                Hosted::CODE => [
                    'redirectUrl' => $this->helperData->getHostedRedirectUrl()
                ]
            ]
        ];
    }
}
