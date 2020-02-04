<?php

namespace Uol\BoaCompra\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Uol\BoaCompra\Model\Payment\CreditCard;
use Uol\BoaCompra\Helper\Data;

class CreditCardConfigProvider implements ConfigProviderInterface
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
     * @var Json
     */
    private $serialized;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CreditCardConfigProvider constructor.
     * @param Session $checkoutSession
     * @param Json $serialized
     * @param Data $helperData
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $checkoutSession,
        Json $serialized,
        Data $helperData,
        LoggerInterface $logger
    ) {
        $this->helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
        $this->serialized = $serialized;
        $this->logger = $logger;
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
                CreditCard::CODE => [
                    'tokenAvailable' => $this->helperData->tokenizationIsAvailable(),
                    'savedCreditCard' => $this->getCreditCardToken(),
                    'brandAndInstallmentsUrl' => $this->helperData->getBrandAndInstallmentUrl(),
                    'installmentsUrl' => $this->helperData->getInstallmentUrl(),
                    'months' => $this->getMonths(),
                    'years' => $this->getYears()
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getMonths()
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July ',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    /**
     * @return array
     */
    public function getYears()
    {
        $years = [];
        $first = (int) date('Y');
        for ($index = 0; $index <= 30; $index++) {
            $year = $first + $index;
            $years[$year] = $year;
        }
        return $years;
    }

    /**
     * @return array|null
     */
    private function getCreditCardToken()
    {
        if (!$this->helperData->tokenizationIsAvailable()) {
            return null;
        }

        $ccToken = null;

        try {
            $customer = $this->checkoutSession->getQuote()->getCustomer();
            if (!$customer->getCustomAttribute('boacompra_cc_token_is_active')) {
                return null;
            }
            if (!$customer->getCustomAttribute('boacompra_cc_token_is_active')->getValue()) {
                return null;
            }
            if (!$customer->getCustomAttribute('boacompra_cc_token')) {
                return null;
            }
            if (!$ccTokenJson = $customer->getCustomAttribute('boacompra_cc_token')->getValue()) {
                return null;
            }
            $ccToken = $this->serialized->unserialize($ccTokenJson);
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
            $this->logger->debug($e->getMessage());
        }

        return $ccToken;
    }
}
