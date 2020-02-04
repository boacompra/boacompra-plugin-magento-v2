<?php

namespace Uol\BoaCompra\Model\Request\Card;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Uol\BoaCompra\Helper\Data;

class BrandAndInstallments
{
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Refund constructor.
     * @param Session $checkoutSession
     * @param Data $helperData
     */
    public function __construct(
        Session $checkoutSession,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param $bin
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function build($bin):array
    {
        $quote = $this->checkoutSession->getQuote();

        $data = [
            'bin' => $bin,
            'country' => $quote->getShippingAddress()->getCountryId(),
            'amount' => $quote->getGrandTotal(),
            'currency' => $quote->getQuoteCurrencyCode()
        ];

        return $data;
    }
}
