<?php

declare(strict_types=1);

namespace Zaproo\Hobby2\Plugin\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\CustomerData\Customer as CustomerData;

class Customer
{
    public function __construct(
        protected CurrentCustomer $currentCustomer,
        protected \Zaproo\Hobby2\Model\Hobby $hobby
    ) {
    }

    public function afterGetSectionData(CustomerData $subject, $result)
    {
        $result['hobby'] = $this->hobby->getUserHobbyText();
        
        return $result;
    }
}