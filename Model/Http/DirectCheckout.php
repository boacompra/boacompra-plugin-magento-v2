<?php

namespace Uol\BoaCompra\Model\Http;

use Magento\Framework\HTTP\ZendClient;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Http\Client\Zend;

class DirectCheckout
{
    const PRODUCTION_ENDPOINT = 'https://api.boacompra.com/transactions/';
    const SANDBOX_ENDPOINT = 'https://api.sandbox.boacompra.com/transactions/';

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
        $jsonData = json_encode($data);

        $header = $this->header->make($this->getEndpoint(), $jsonData, 'v2')->generateHeader();

        $zend = $this->zend->logContext('DIRECT');

        return $zend->placeRequest(ZendClient::POST, $this->getEndpoint(), $jsonData, $header);
    }

    /**
     * @return string
     */
    private function getEndpoint()
    {
        return ($this->helperData->isSandboxMode())
            ? self::SANDBOX_ENDPOINT
            : self::PRODUCTION_ENDPOINT
            ;
    }
}
