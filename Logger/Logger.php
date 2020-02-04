<?php

namespace Uol\BoaCompra\Logger;

use Uol\BoaCompra\Helper\Data;

class Logger extends \Monolog\Logger
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * Logger constructor.
     * @param string $name
     * @param array $handlers
     * @param array $processors
     * @param Data $helperData
     */
    public function __construct(
        $name,
        Data $helperData,
        array $handlers = [],
        array $processors = []
    ) {
        parent::__construct($name, $handlers, $processors);
        $this->helperData = $helperData;
    }

    /**
     * @inheritdoc
     */
    public function addRecord($level, $message, array $context = array())
    {
        if (!$this->helperData->enableLog()) {
            return false;
        }

        return parent::addRecord($level, $message, $context);
    }
}
