<?php

namespace Uol\BoaCompra\Controller\Cc;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Uol\BoaCompra\Helper\Installments as HelperInstallments;
use Uol\BoaCompra\Model\Http\Installments as InstallmentsHttp;
use Uol\BoaCompra\Model\Request\Card\Installments as InstallmentsData;

class Installments extends Action implements CsrfAwareActionInterface
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var InstallmentsData
     */
    private $installmentsData;
    /**
     * @var InstallmentsHttp
     */
    private $installmentsHttp;
    /**
     * @var HelperInstallments
     */
    private $helperInstallments;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Index constructor.
     * @param Context $context
     * @param InstallmentsData $installmentsData
     * @param InstallmentsHttp $installmentsHttp
     * @param HelperInstallments $helperInstallments
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        InstallmentsData $installmentsData,
        InstallmentsHttp $installmentsHttp,
        HelperInstallments $helperInstallments,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->installmentsData = $installmentsData;
        $this->installmentsHttp = $installmentsHttp;
        $this->helperInstallments = $helperInstallments;
        $this->logger = $logger;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $data = [];
        $brand = $this->getRequest()->getParam('brand');

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);

        try {
            $requestData = $this->installmentsData->build($brand);
            $result = $this->installmentsHttp->create($requestData);

            $data = json_decode($result, true);
        } catch (NoSuchEntityException $e) {
        } catch (LocalizedException $e) {
            $this->logger->debug($e->getMessage(), [
                'context' => $e
            ]);

            return $resultJson->setData([
                'error' => true,
                'message' => __('Installments error')
            ]);
        }

        if ($message = $this->handleErrors($data)) {
            return $resultJson->setData([
                'error' => true,
                'message' => $message
            ]);
        }

        $data['installments'] = $this->helperInstallments->prepare($data['installments']);

        return $resultJson->setData($data);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function handleErrors(array $data)
    {
        if (!array_key_exists('errors', $data)) {
            return false;
        }

        $error = reset($data['errors']);
        switch ($error['description']) {
            case 'bin_missing':
                return __('Missing Bin');
            case 'bin_invalid':
                return __('Invalid Bin');
            case 'brand_missing':
                return __('Missing Brand');
            case 'brand_invalid':
                return __('Invalid Brand');
            case 'country_missing':
                return __('Missing Country');
            case 'country_invalid':
                return __('Invalid Country');
            case 'amount_missing':
                return __('Missing Amount');
            case 'amount_invalid':
                return __('Missing Amount');
            case 'currency_missing':
                return __('Missing Currency');
            case 'currency_invalid':
                return __('Invalid Currency');
            case 'brand_not_accepted':
                return __('Brand not accepted');
            default:
                return __('Installments error');
        }
    }
}
