<?php

namespace Uol\BoaCompra\Model\Data;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Customer
{
    /**
     * @var OrderInterface $order
     */
    private $order;
    /**
     * @var OrderAddressInterface $billingAddress
     */
    private $billingAddress;

    /**
     * @param OrderInterface $order
     * @return Customer
     */
    public function extract(OrderInterface $order)
    {
        $this->order = $order;
        $this->billingAddress = $order->getBillingAddress();

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return substr($this->order->getBillingAddress()->getEmail(), 0, 60);
    }

    /**
     * @return string
     */
    public function getName()
    {
        $customerName = trim($this->billingAddress->getFirstname() . ' ' . $this->billingAddress->getLastname());

        return substr($customerName, 0, 60);
    }

    /**
     * @return int
     */
    public function getTelephone()
    {
        $telephone = preg_replace("/[^0-9]/", "", $this->billingAddress->getTelephone());

        return substr($telephone, 0, 20);
    }

    /**
     * @return string
     */
    public function getDocument()
    {
        $document = preg_replace("/[^0-9]/", "", $this->order->getCustomerTaxvat());

        return substr($document, 0, 20);
    }

    /**
     * @return string
     */
    public function getDocumentType()
    {
        return (strlen($this->getDocument()) > 11) ? 'cnpj' : 'cpf';
    }
}
