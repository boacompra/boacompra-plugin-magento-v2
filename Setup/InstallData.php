<?php

namespace Uol\BoaCompra\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;

    /**
     * @inheritDoc
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(Customer::ENTITY, 'boacompra_cc_token', [
            'type' => 'text',
            'label' => 'boacompra_cc_token',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => false,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'boacompra_cc_token_is_active', [
            'type' => 'int',
            'label' => 'boacompra_cc_token_is_active',
            'input' => 'boolean',
            'source' => '',
            'required' => false,
            'visible' => false,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);
    }
}