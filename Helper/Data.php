<?php

namespace Uol\BoaCompra\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Uol\BoaCompra\Model\Config\Source\CheckoutType;
use Uol\BoaCompra\Model\Config\Source\Environment;

class Data extends AbstractHelper
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $field
     * @return mixed
     */
    public function getConfig($field)
    {
        return $this->scopeConfig->getValue(
            'payment/boacompra/'.$field,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getConfig('active');
    }

    /**
     * @return bool
     */
    public function isSandboxMode()
    {
        return (bool) ($this->getConfig('environment') == Environment::SANDBOX);
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getConfig('merchant_id');
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        $secretKey = $this->getConfig('secret_key');

        return $this->encryptor->decrypt($secretKey);
    }

    /**
     * @return bool
     */
    public function isHostedType()
    {
        return (bool) ($this->getConfig('checkout_type') == CheckoutType::HOSTED);
    }

    /**
     * @return bool
     */
    public function isDirectCheckoutType()
    {
        return (bool) ($this->getConfig('checkout_type') == CheckoutType::DIRECT_PAYMENT);
    }

    /**
     * @return array
     */
    public function getAvailableDirectCheckoutTypes()
    {
        $availableTypes = $this->getConfig('payment_type');

        return (empty($availableTypes)) ? [] : explode(',', $availableTypes);
    }

    /**
     * @return bool
     */
    public function tokenizationIsAvailable()
    {
        return (bool) $this->getConfig('allow_tokenization');
    }

    /**
     * @return bool
     */
    public function enableLog()
    {
        return (bool) $this->getConfig('log');
    }

    /**
     * @return string
     */
    public function getBrandAndInstallmentUrl()
    {
        return $this->_getUrl('', ['_direct' => 'boacompra/cc/brandandinstallments', '_secure' => true]);
    }

    /**
     * @return string
     */
    public function getInstallmentUrl()
    {
        return $this->_getUrl('', ['_direct' => 'boacompra/cc/installments', '_secure' => true]);
    }


    /**
     * @return string
     */
    public function getEWalletRedirectUrl()
    {
        return $this->_getUrl('', ['_direct' => 'boacompra/ewallet', '_secure' => true]);
    }

    /**
     * @return string
     */
    public function getHostedRedirectUrl()
    {
        return $this->_getUrl('', ['_direct' => 'boacompra/hosted','_secure' => true]);
    }

    /**
     * @return string
     */
    public function getNotificationUrl()
    {
        return $this->_getUrl('', ['_direct' => 'boacompra/notification','_secure' => true]);
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->_getUrl('', ['_direct' => 'boacompra/success', '_secure' => true]);
    }

    /**
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->_getUrl('', ['_direct' => 'boacompra/error', '_secure' => true]);
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrencyCode()
    {
        try {
            return $this->storeManager->getStore()->getCurrentCurrencyCode();
        } catch (NoSuchEntityException $e) {
        }

        return '';
    }
}
