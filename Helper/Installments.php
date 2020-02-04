<?php

namespace Uol\BoaCompra\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Installments
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Installments constructor.
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param array $installments
     * @return array
     */
    public function prepare($installments = [])
    {
        $data = [];
        foreach ($installments as $item) {
            $data[] = array_merge($item, [
                'label' => $this->handleLabel($item)
            ]);
        }

        return $data;
    }

    /**
     * @param $item
     * @return string
     */
    private function handleLabel($item)
    {
        $installmentAmount = $this->getCurrency($item['installmentAmount']);

        $quantity = $item['quantity'].'x ';
        $totalAmount = ($item['interestFree'])
            ? __('Interest-free')
            : __('Total') . ' '.$this->getCurrency($item['totalAmount'])
        ;

        return sprintf('%s %s %s', $quantity, $installmentAmount, $totalAmount);
    }

    /**
     * @param $price
     * @return string
     */
    private function getCurrency($price)
    {
        try {
            $symbol = $this->storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
        } catch (NoSuchEntityException $e) {
            $symbol = 'R$ ';
        }

        return sprintf('%s %s',
            $symbol,
            ($symbol === 'R$')
                ? number_format($price, 2, ',', '.')
                : number_format($price, 2)
            );
    }
}
