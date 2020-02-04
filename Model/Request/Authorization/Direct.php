<?php

namespace Uol\BoaCompra\Model\Request\Authorization;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Locale\Resolver;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Uol\BoaCompra\Enum\DataAssign;
use Uol\BoaCompra\Enum\Language;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Data\Address as AddressData;
use Uol\BoaCompra\Model\Data\Customer as CustomerData;

class Direct
{
    const TRANSACTION = 'transaction';
    const REFERENCE = 'reference';
    const COUNTRY = 'country';
    const CHECKOUT_TYPE = 'checkout-type';
    const NOTIFY_URL = 'notify_url';
    const LANGUAGE = 'language';
    const CHARGE = 'charge';
    const NOTIFICATION_URL = 'notification-url';
    const AMOUNT = 'amount';
    const PAYMENT_METHOD = 'payment-method';
    const PAYMENT_METHOD_TYPE = 'type';
    const PAYMENT_METHOD_SUB_TYPE = 'sub-type';
    const PAYER = 'payer';
    const NAME = 'name';
    const EMAIL = 'email';
    const PHONE_NUMBER = 'phone-number';
    const DOCUMENT = 'document';
    const DOCUMENT_TYPE = 'type';
    const DOCUMENT_NUMBER = 'number';
    const ADDRESS = 'address';
    const STREET = 'street';
    const STREET_NUMBER = 'number';
    const COMPLEMENT = 'complement';
    const DISTRICT = 'district';
    const STATE = 'state';
    const CITY = 'city';
    const POSTCODE = 'zip-code';
    const IP = 'ip';
    const CART = 'cart';
    const CURRENCY = 'currency';
    const SHIPPING = 'shipping';
    const SHIPPING_COST = 'cost';
    const QUANTITY = 'quantity';
    const DESCRIPTION = 'description';
    const ITEM_TYPE = 'type';
    const UNIT_PRICE = 'unit-price';
    const CHECKOUT_DIRECT = 'direct';
    const ITEM_TYPE_PHYSICAL = 'physical';

    /**
     * @var Data
     */
    public $helperData;
    /**
     * @var CustomerData
     */
    private $customerData;
    /**
     * @var AddressData
     */
    private $addressData;
    /**
     * @var Resolver
     */
    private $store;
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * PostPay constructor.
     * @param CustomerData $customerData
     * @param AddressData $addressData
     * @param Resolver $store
     * @param RemoteAddress $remoteAddress
     * @param Data $helperData
     */
    public function __construct(
        CustomerData $customerData,
        AddressData $addressData,
        Resolver $store,
        RemoteAddress $remoteAddress,
        Data $helperData
    ) {
        $this->helperData = $helperData;
        $this->customerData = $customerData;
        $this->addressData = $addressData;
        $this->store = $store;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @param InfoInterface $payment
     * @return array
     */
    public function build(InfoInterface $payment)
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $customer = $this->customerData->extract($order);
        $billingAddress = $this->addressData->extract($order->getBillingAddress());
        $shippingAddress = $this->addressData->extract($order->getShippingAddress());

        $data = [
            self::TRANSACTION => [
                self::REFERENCE => $order->getIncrementId(),
                self::COUNTRY => $billingAddress->getCountry(),
                self::CURRENCY => $order->getOrderCurrencyCode(),
                self::CHECKOUT_TYPE => self::CHECKOUT_DIRECT,
                self::NOTIFICATION_URL => $this->helperData->getNotificationUrl(),
                self::LANGUAGE => $this->getCurrentLanguage()
            ],
            self::CHARGE => [
                [
                    self::AMOUNT => $order->getGrandTotal(),
                    self::PAYMENT_METHOD => [
                        self::PAYMENT_METHOD_TYPE => $payment->getAdditionalInformation(
                            DataAssign::PAYMENT_METHOD_TYPE
                        ),
                        self::PAYMENT_METHOD_SUB_TYPE => $payment->getAdditionalInformation(
                            DataAssign::PAYMENT_METHOD_SUB_TYPE
                        )
                    ]
                ]
            ],
            self::PAYER => [
                self::NAME => $customer->getName(),
                self::EMAIL => $customer->getEmail(),
                self::PHONE_NUMBER => $billingAddress->getTelephone(),
                self::DOCUMENT => [
                    self::DOCUMENT_TYPE => $customer->getDocumentType(),
                    self::DOCUMENT_NUMBER => $customer->getDocument()
                ],
                self::ADDRESS => [
                    self::STREET => $billingAddress->getStreet(),
                    self::STREET_NUMBER => $billingAddress->getNumber(),
                    self::COMPLEMENT => $billingAddress->getComplement(),
                    self::DISTRICT => $billingAddress->getNeighborhood(),
                    self::STATE => $billingAddress->getState(),
                    self::CITY => $billingAddress->getCity(),
                    self::COUNTRY => $billingAddress->getCountry(),
                    self::POSTCODE => $billingAddress->getPostcode(),
                ],
                self::IP => $this->getRemoteAddress()
            ],
            self::SHIPPING => [
                self::SHIPPING_COST => $order->getShippingAmount(),
                self::ADDRESS => [
                    self::STREET => $shippingAddress->getStreet(),
                    self::STREET_NUMBER => $shippingAddress->getNumber(),
                    self::COMPLEMENT => $shippingAddress->getComplement(),
                    self::DISTRICT => $shippingAddress->getNeighborhood(),
                    self::STATE => $shippingAddress->getState(),
                    self::CITY => $shippingAddress->getCity(),
                    self::COUNTRY => $shippingAddress->getCountry(),
                    self::POSTCODE => $shippingAddress->getPostcode(),
                ],
            ],
            self::CART => $this->extractItems($order->getItems())
        ];

        return $data;
    }

    /**
     * @param $items
     * @return array
     */
    private function extractItems($items)
    {
        $data = [];

        foreach ($items as $item) {
            if (!$item->getPrice()) {
                continue;
            }

            $data[] = [
                self::QUANTITY => $item->getQtyOrdered(),
                self::DESCRIPTION => $item->getName(),
                self::ITEM_TYPE => self::ITEM_TYPE_PHYSICAL,
                self::UNIT_PRICE => $item->getPrice(),
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    private function getCurrentLanguage()
    {
        return Language::getCode($this->store->getLocale());
    }

    /**
     * @return string
     */
    private function getRemoteAddress()
    {
        return $this->remoteAddress->getRemoteAddress() ?? '0.0.0.0';
    }
}
