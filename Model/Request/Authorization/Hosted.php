<?php

namespace Uol\BoaCompra\Model\Request\Authorization;

use Magento\Sales\Api\Data\OrderInterface;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Data\Address as AddressData;
use Uol\BoaCompra\Model\Data\Customer as CustomerData;

class Hosted
{
    const STORE_ID = 'store_id';
    const RETURN_URL = 'return';
    const NOTIFY_URL = 'notify_url';
    const CLIENT_EMAIL = 'client_email';
    const CLIENT_NAME = 'client_name';
    const CLIENT_ZIP_CODE = 'client_zip_code';
    const CLIENT_STREET = 'client_street';
    const CLIENT_NUMBER = 'client_number';
    const CLIENT_SUBURB = 'client_suburb';
    const CLIENT_CITY = 'client_city';
    const CLIENT_STATE = 'client_state';
    const CLIENT_COUNTRY = 'client_country';
    const CLIENT_TELEPHONE = 'client_telephone';
    const CLIENT_CPF = 'client_cpf';
    const TEST_MODE = 'test_mode';
    const HASH_KEY = 'hash_key';
    const ORDER_ID = 'order_id';
    const ORDER_DESCRIPTION = 'order_description';
    const AMOUNT = 'amount';
    const CURRENCY_CODE = 'currency_code';
    const COUNTRY_PAYMENT = 'country_payment';

    /**
     * @var CustomerData
     */
    private $customerData;
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var AddressData
     */
    private $addressData;

    /**
     * Hosted constructor.
     * @param CustomerData $customerData
     * @param AddressData $addressData
     * @param Data $helperData
     */
    public function __construct(
        CustomerData $customerData,
        AddressData $addressData,
        Data $helperData
    ) {
        $this->customerData = $customerData;
        $this->helperData = $helperData;
        $this->addressData = $addressData;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function build(OrderInterface $order)
    {
        $customer = $this->customerData->extract($order);
        $billingAddress = $this->addressData->extract($order->getBillingAddress());

        $data = [
            self::TEST_MODE => (int) $this->helperData->isSandboxMode(),
            self::STORE_ID => $this->helperData->getMerchantId(),
            self::RETURN_URL => $this->helperData->getSuccessUrl(),
            self::NOTIFY_URL => $this->helperData->getNotificationUrl(),
            self::CLIENT_EMAIL => $customer->getEmail(),
            self::CLIENT_NAME => $customer->getName(),
            self::CLIENT_TELEPHONE => $customer->getTelephone(),
            self::CLIENT_ZIP_CODE => $billingAddress->getPostcode(),
            self::CLIENT_CITY => $billingAddress->getCity(),
            self::CLIENT_STATE => $billingAddress->getState(),
            self::CLIENT_COUNTRY => $billingAddress->getCountry(),
            self::ORDER_ID => $order->getIncrementId(),
            self::ORDER_DESCRIPTION => $this->getDescription($order->getItems()),
            self::AMOUNT => number_format((float)$order->getGrandTotal(), 2, '.', ''),
            self::CURRENCY_CODE => $order->getOrderCurrencyCode(),
            self::COUNTRY_PAYMENT => $billingAddress->getCountry()
        ];

        $data = array_merge($data, [
            self::HASH_KEY => $this->generateHash($data)
        ]);

        return $data;
    }

    /**
     * @param $items
     * @return bool|string
     */
    private function getDescription($items)
    {
        $itemsName = [];
        foreach ($items as $item) {
            array_push($itemsName, sprintf('(%s) %s', intval($item->getQtyOrdered()), $item->getName()));
        }

        return substr(implode(', ', $itemsName), 0, 400);
    }

    /**
     * @param array $data
     * @return string
     */
    private function generateHash($data = [])
    {
        return md5($data[self::STORE_ID]
            . $data[self::NOTIFY_URL]
            . $data[self::ORDER_ID]
            . $data[self::AMOUNT]
            . $data[self::CURRENCY_CODE]
            . $this->helperData->getSecretKey()
        );
    }
}
