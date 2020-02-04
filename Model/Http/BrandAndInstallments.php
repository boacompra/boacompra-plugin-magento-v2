<?php

namespace Uol\BoaCompra\Model\Http;

use Magento\Framework\HTTP\ZendClient;
use Uol\BoaCompra\Helper\Data;
use Uol\BoaCompra\Model\Http\Client\Zend;

class BrandAndInstallments
{
    const END_POINT = 'https://api.boacompra.com/card';

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
        $endPoint = self::END_POINT . '?' . http_build_query($data);
        $header = $this->header->make($endPoint)->generateHeader();

        $zend = $this->zend->logContext('CARD');

        return $zend->placeRequest(ZendClient::GET, $endPoint, '', $header);
    }
}
