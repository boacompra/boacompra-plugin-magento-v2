<?php

namespace Uol\BoaCompra\Model\Http;

use Uol\BoaCompra\Helper\Data;

class Header
{
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var string
     */
    private $contentMd5;
    /**
     * @var string
     */
    private $httpVerb;
    /**
     * @var string
     */
    private $apiVersion;

    /**
     * Header constructor.
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param $url
     * @param string $content
     * @param string $apiVersion
     * @return $this
     */
    public function make($url, $content = '', $apiVersion = 'v1')
    {
        $this->setContentMd5($content);
        $this->setHttpVerb($url);
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * @param $content
     */
    private function setContentMd5($content)
    {
        $this->contentMd5 = ($content == '') ? '' : md5($content);
    }

    /**
     * @param $url
     * @return string
     */
    private function getQueryString($url)
    {
        $queryString = parse_url($url, PHP_URL_QUERY);

        return empty($queryString) ? '' : '?' . $queryString;
    }

    /**
     * @param $url
     */
    private function setHttpVerb($url)
    {
        $this->httpVerb = parse_url($url, PHP_URL_PATH) . $this->getQueryString($url);
    }

    /**
     * @return string
     */
    private function generateAuthorization()
    {
        return hash_hmac('sha256', $this->httpVerb . $this->contentMd5, $this->helperData->getSecretKey());
    }

    /**
     * @return array
     */
    public function generateHeader()
    {
        $headers = [
            'Accept' => 'application/vnd.boacompra.com.'.$this->apiVersion.'+json; charset=UTF-8',
            'Content-Type' => 'application/json',
            'Content-MD5' => $this->contentMd5,
            'Authorization' => $this->helperData->getMerchantId().':'.$this->generateAuthorization(),
            'Accept-Language' => 'en-US'
        ];

        return $headers;
    }
}
