<?php

namespace Uol\BoaCompra\Model\Http;

use Magento\Framework\HTTP\ZendClient;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Http\Client\Zend;

class Refund
{
    const END_POINT = 'https://api.boacompra.com/refunds';

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

        $header = $this->header->make(self::END_POINT, $jsonData, 'v2')->generateHeader();

        $zend = $this->zend->logContext('REFUND');

        return $zend->placeRequest(ZendClient::POST, self::END_POINT, $jsonData, $header);
    }
}
