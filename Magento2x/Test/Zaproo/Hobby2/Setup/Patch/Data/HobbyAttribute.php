<?php

declare(strict_types=1);

namespace Zaproo\Hobby2\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Customer\Model\ResourceModel\Attribute as ResourceModelAttribute;

class HobbyAttribute implements DataPatchInterface, PatchVersionInterface
{
    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup,
        private CustomerSetupFactory $customerSetupFactory,
        private ResourceModelAttribute $resourceModelAttribute,
        private AttributeSetFactory $attributeSetFactory
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        opcache_reset();
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->addHobbyAttribute();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function addHobbyAttribute()
    {
        $attributeCode = 'hobby2';
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeSetId = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY)->getDefaultAttributeSetId();
        $attributeGroupId = $this->attributeSetFactory->create()->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            $attributeCode,
            [
                'type' => 'int',
                'label' => 'Hobby2',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'required' => false,
                'sort_order' => 100,
                'visible' => true,
                'system' => false,
                'validate_rules' => '[]',
                'position' => 110,
                'option' => ['values' => ['Yoga', 'Traveling', 'Hiking']]
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode);
        $attribute->setData('used_in_forms', ['adminhtml_customer', 'customer_account_edit']);
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);
        $this->resourceModelAttribute->save($attribute);
    }

    public static function getDependencies()
    {
        return [];
    }

    public static function getVersion()
    {
        return '1.0.1';
    }

    public function getAliases()
    {
        return [];
    }
}