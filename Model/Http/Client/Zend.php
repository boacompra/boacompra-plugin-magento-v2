<?php

namespace Uol\BoaCompra\Model\Http\Client;

use Exception;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Uol\BoaCompra\Logger\Logger;
use Zend_Http_Client;
use LogicException;

class Zend
{
    /**
     * @var ZendClientFactory
     */
    private $zendClientFactory;
    /**
     * @var String
     */
    private $context;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Client constructor.
     * @param ZendClientFactory $zendClientFactory
     * @param Logger $logger
     */
    public function __construct(
        ZendClientFactory $zendClientFactory,
        Logger $logger
    ) {
        $this->zendClientFactory = $zendClientFactory;
        $this->logger = $logger;
    }

    public function placeRequest($method, $uri, $body = '', $headers = [])
    {
        $this->logger->debug(__('%1 - Request %2 to URI %3', $this->context, $method, $uri), [
            'headers' => $headers,
            'uri' => $uri,
            'body' => $body,
            'method' => $method
        ]);

        /** @var ZendClient $client */
        $client = $this->zendClientFactory->create();
        $client->setMethod($method);

        switch ($method) {
            case Zend_Http_Client::GET:
                $client->setParameterGet($body);
                break;
            case Zend_Http_Client::POST:
                $client->setRawData($body);
                break;
            default:
                throw new LogicException(__('Unsupported HTTP method %1', $method));
        }

        $client->setHeaders($headers);
        $client->setUri($uri);

        $result = '';

        try {
            $response = $client->request();
            $result = $response->getBody();

            $this->logger->debug(__('%1 - Response to URI %2', $this->context, $uri), [
                'response' => $response
            ]);
        } catch (Exception $e) {
            $this->logger->debug(__('%1 - Error to URI %2', $this->context, $uri), [
                'exception' => $e->getMessage()
            ]);
        }

        return $result;
    }

    /**
     * @param $context
     * @return $this
     */
    public function logContext($context)
    {
        $this->context = $context;

        return $this;
    }
}
