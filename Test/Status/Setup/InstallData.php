<?php

namespace Test\Status\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    private $customerSetupFactory;

    public function __construct( \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory )
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup, ModuleContextInterface $context
    ) {
        /** @var CustomerSetup $customerSetup */

        $customerSetup = $this->customerSetupFactory->create( [ 'setup' => $setup ] );
        $setup->startSetup();

        $attributeCode = "custom_status";

        $customerSetup->removeAttribute( \Magento\Customer\Model\Customer::ENTITY, $attributeCode );

        $customerSetup->addAttribute( 'customer',
            $attributeCode, [
                'label'          => 'Status',
                'type'           => 'text',
                'frontend_input' => 'text',
                'required'       => false,
                'visible'        => true,
                'system'         => 0,
                'position'       => 110,
            ] );

        $loyaltyAttribute = $customerSetup->getEavConfig()->getAttribute( 'customer', $attributeCode );
        $loyaltyAttribute->setData( 'used_in_forms', [ 'adminhtml_customer' ] );
        $loyaltyAttribute->save();

        $setup->endSetup();
    }
}