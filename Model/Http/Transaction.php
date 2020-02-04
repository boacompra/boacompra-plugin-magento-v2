<?php

namespace Uol\BoaCompra\Model\Http;

use Magento\Framework\HTTP\ZendClient;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Http\Client\Zend;

class Transaction
{
    const PRODUCTION_ENDPOINT = 'https://api.boacompra.com/transactions';
    const SANDBOX_ENDPOINT = 'https://api.sandbox.boacompra.com/transactions';

    /**
     * @var Zend
     */
    private $zend;
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var Header
     */
    private $header;

    /**
     * Notification constructor.
     * @param Zend $zend
     * @param Data $helperData
     * @param Header $header
     */
    public function __construct(
        Zend $zend,
        Data $helperData,
        Header $header
    ) {
        $this->zend = $zend;
        $this->helperData = $helperData;
        $this->header = $header;
    }

    /**
     * @param array $data
     * @return array|string
     */
    public function create($data = [])
    {
        $header = $this->header->make($this->getEndpoint($data))->generateHeader();

        return $this->zend->logContext('TRANSACTION')
            ->placeRequest(ZendClient::GET, $this->getEndpoint($data), '', $header)
            ;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getEndpoint($data = [])
    {
        if (array_key_exists('initial-order-date', $data)) {
            return self::PRODUCTION_ENDPOINT . '?' . http_build_query($data);
        }

        if ($this->helperData->isSandboxMode()) {
            return self::SANDBOX_ENDPOINT . '/' . $data['transaction-code'];
        }

        return self::PRODUCTION_ENDPOINT . '/' . $data['transaction-code'];
    }
}
