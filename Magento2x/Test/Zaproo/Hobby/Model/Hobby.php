<?php

declare(strict_types=1);

namespace Zaproo\Hobby\Model;

use \Magento\Customer\Model\CustomerFactory;
use \Magento\Customer\Model\Customer;
use \Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use \Magento\Eav\Model\Config as EavConfig;
use \Magento\Customer\Model\Session as CustomerSession;

class Hobby
{
    const ATTRCODE = 'hobby';

    public function __construct(
        protected CustomerFactory $customerFactory,
        protected CustomerResource $customerResource,
        protected EavConfig $eavConfig,
        protected CustomerSession $customerSession

    ) {
    }

    public function saveHobby(int $hobby, int $customerId = null)
    {
        $customerId = $customerId ?? $this->customerSession->getId();

        if ($customerId) {
            $customer = $this->customerFactory->create();
            $this->customerResource->load($customer, $customerId);

            $customerData = $customer->getDataModel();
            $customerData->setCustomAttribute(self::ATTRCODE, $hobby);

            $customer->updateData($customerData);

            $this->customerResource->saveAttribute($customer, self::ATTRCODE);
        }
    }

    public function getHobbies(): array
    {
        $hobbies = [];

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, self::ATTRCODE);

        if ($attribute->usesSource()) {
            $hobbies = $attribute->getSource()->getAllOptions();
            if (count($hobbies) > 0) {
                $hobbies = array_combine(
                    array_map(
                        function ($hobbyId) {
                            return (int) $hobbyId;
                        },
                        array_column($hobbies, 'value')
                    ),
                    array_column($hobbies, 'label')
                );
            }
        }

        return $hobbies;
    }

    public function getHobby(): int
    {
        $hobby = 0;
        $customerId = $this->customerSession->getId();

        if ($customerId) {
            $customer = $this->customerSession->getCustomer();
            $hobby = (int) $customer->getData(self::ATTRCODE);
        }

        return $hobby;
    }

    public function getHobbyText(): string
    {
        $hobby = '';
        $customerId = $this->customerSession->getId();

        if ($customerId) {
            $customer = $this->customerSession->getCustomer();
            $attributeValue = (int) $customer->getData(self::ATTRCODE);
            $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, self::ATTRCODE);
            $hobby = (string) $attribute->getSource()->getOptionText($attributeValue);
        }

        return $hobby;
    }
}
