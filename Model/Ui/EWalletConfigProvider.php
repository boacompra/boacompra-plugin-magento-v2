<?php

namespace Uol\BoaCompra\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Payment\EWallet;

class EWalletConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var Repository
     */
    private $assetRepository;

    /**
     * EWalletConfigProvider constructor.
     * @param Repository $assetRepository
     * @param Data $helperData
     */
    public function __construct(
        Repository $assetRepository,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->assetRepository = $assetRepository;
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
                EWallet::CODE => [
                    'redirectUrl' => $this->helperData->getEWalletRedirectUrl(),
                    'subtypes' => [
                        [
                            'name' => 'pagseguro',
                            'image' => $this->assetRepository->getUrl('Uol_BoaCompra::images/pagseguro-logo.png')
                        ],
                        [
                            'name' => 'paypal',
                            'image' => $this->assetRepository->getUrl('Uol_BoaCompra::images/paypal-logo.png')
                        ],
                    ]
                ]
            ]
        ];
    }
}
