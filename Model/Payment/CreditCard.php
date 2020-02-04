<?php

namespace Uol\BoaCompra\Model\Payment;

use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Uol\BoaCompra\Block\Info\Cc;

class CreditCard extends AbstractMethod
{
    const CODE = 'boacompra_creditcard';

    protected $_code = self::CODE;
    protected $_infoBlockType = Cc::class;

    /**
     * @inheritDoc
     */
    public function assignData(DataObject $data)
    {
        $info = $this->getInfoInstance();

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_object($additionalData)) {
            $additionalData = new DataObject($additionalData ?: []);
        }

        $info->setAdditionalInformation('installments', $additionalData->getCcInstallments());

        $info->addData(
            [
                'cc_type' => $additionalData->getCcType(),
                'cc_last_4' => $additionalData->getCcLast4(),
                'cc_cid' => $additionalData->getCcCid(),
                'cc_exp_month' => $additionalData->getCcExpMonth(),
                'cc_exp_year' => $additionalData->getCcExpYear(),
                'cc_installments' => $additionalData->getCcInstallments(),
            ]
        );

        parent::assignData($data);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAvailable(CartInterface $quote = null)
    {
        if (!$this->helperData->isDirectCheckoutType()) {
            return false;
        }

        if (!$this->isAvailableDirectCheckoutType()) {
            return false;
        }

        return $this->helperData->isActive();
    }
}
