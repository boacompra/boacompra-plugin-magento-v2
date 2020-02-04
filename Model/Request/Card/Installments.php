<?php

namespace Uol\BoaCompra\Model\Request\Card;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Uol\BoaCompra\Helper\Data;

class Installments
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
     * @param $brand
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function build($brand):array
    {
        $quote = $this->checkoutSession->getQuote();

        $data = [
            'brand' => $brand,
            'country' => $quote->getShippingAddress()->getCountryId(),
            'amount' => $quote->getGrandTotal(),
            'currency' => $quote->getQuoteCurrencyCode()
        ];

        return $data;
    }
}
