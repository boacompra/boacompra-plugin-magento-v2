<?php

namespace Uol\BoaCompra\Model\Data;

use Magento\Directory\Model\Region;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Uol\BoaCompra\Enum\StreetLine;

class Address
{
    /**
     * @var OrderAddressInterface|null
     */
    private $address;

    /**
     * @param OrderAddressInterface $address
     * @return $this
     */
    public function extract(OrderAddressInterface $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        $arrStreet = $this->address->getStreet();
        if (!array_key_exists(StreetLine::STREET, $arrStreet)) {
            return '';
        }

        return substr($arrStreet[StreetLine::STREET], 0, 60);
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        $arrStreet = $this->address->getStreet();
        if (!array_key_exists(StreetLine::NUMBER, $arrStreet)) {
            return '';
        }

        return substr($arrStreet[StreetLine::NUMBER], 0, 10);
    }

    /**
     * @return string
     */
    public function getNeighborhood()
    {
        $arrStreet = $this->address->getStreet();
        if (!array_key_exists(StreetLine::NEIGHBORHOOD, $arrStreet)) {
            return '';
        }

        return substr($arrStreet[StreetLine::NEIGHBORHOOD], 0, 60);
    }

    /**
     * @return string
     */
    public function getComplement()
    {
        $arrStreet = $this->address->getStreet();
        if (!array_key_exists(StreetLine::COMPLEMENT, $arrStreet)) {
            return '';
        }

        return substr($arrStreet[StreetLine::COMPLEMENT], 0, 60);
    }

    /**
     * @return string
     */
    public function getState()
    {
        $objectManager = ObjectManager::getInstance();

        /** @var Region $oRegion */
        $oRegion = $objectManager->create('Magento\Directory\Model\Region');
        $region = $oRegion->loadByCode(
            $this->address->getRegionCode(),
            $this->address->getCountryId()
        );

        return $region['code'];
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->address->getCity();
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->address->getCountryId();
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        $postcode = preg_replace("/[^0-9]/", "", $this->address->getPostcode());

        return (string) substr($postcode, 0, 8);
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        $phone = preg_replace("/[^0-9]/", "", $this->address->getTelephone());

        return sprintf('+55%s', substr($phone, 0, 11));
    }
}
