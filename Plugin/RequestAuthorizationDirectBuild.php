<?php

namespace Uol\BoaCompra\Plugin;

use Magento\Payment\Model\InfoInterface;
use Uol\BoaCompra\Enum\DataAssign;
use Uol\BoaCompra\Model\Request\Authorization\Direct;

class RequestAuthorizationDirectBuild
{
    public function afterBuild(
        Direct $subject,
        $result,
        InfoInterface $payment
    ) {
        if ($result['charge'][0]['payment-method']['type'] == 'e-wallet') {
            $result['transaction']['redirect-urls'] = [
                'success' => $subject->helperData->getSuccessUrl(),
                'fail' => $subject->helperData->getErrorUrl()
            ];

            return $result;
        }

        if ($result['charge'][0]['payment-method']['type'] == 'credit-card') {
            // Use saved credit card
            if ($payment->getAdditionalInformation(DataAssign::USE_SAVED_CC)) {
                $result['charge'][0]['payment-info'] = [
                    'installments' => (int) $payment->getAdditionalInformation(DataAssign::CC_INSTALLMENTS),
                    'code' => $payment->getAdditionalInformation(DataAssign::CC_CODE)
                ];

                return $result;
            }

            // Use new credit card
            $result['charge'][0]['payment-info'] = [
                'installments' => (int) $payment->getAdditionalInformation(DataAssign::CC_INSTALLMENTS),
                'token' => $payment->getAdditionalInformation(DataAssign::CC_TOKEN)
            ];

            if (!$subject->helperData->tokenizationIsAvailable()) {
                return $result;
            }

            if ($payment->getAdditionalInformation(DataAssign::CC_SAVE)) {
                $result['charge'][0]['payment-info']['save'] = true;
            }

            return $result;
        }

        return $result;
    }
}
