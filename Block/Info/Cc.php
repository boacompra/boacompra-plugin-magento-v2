<?php

namespace Uol\BoaCompra\Block\Info;

class Cc extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @inheritDoc
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);

        $data = [];

        if ($ccType = $this->getCcTypeName()) {
            $data[(string)__('Credit Card Type')] = $ccType;
        }

        if ($this->getInfo()->getCcLast4()) {
            $data[(string)__('Credit Card Number')] = sprintf('xxxx-%s', $this->getInfo()->getCcLast4());
        }

        if ($this->hasCcExpDate()) {
            $year = $this->getInfo()->getCcExpYear();
            $month = $this->getCcExpMonth();
            $data[(string)__('Expiration Date')] = $this->_formatCardDate($year, $month);
        }

        if ($this->getInfo()->getAdditionalInformation('installments')) {
            $data[(string)__('Installments')] = sprintf('%sx',
                $this->getInfo()->getAdditionalInformation('installments')
            );
        }

        return $transport->setData(array_merge($data, $transport->getData()));
    }
}
