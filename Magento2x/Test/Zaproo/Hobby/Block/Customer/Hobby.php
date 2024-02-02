<?php

declare(strict_types=1);

namespace Zaproo\Hobby\Block\Customer;

class Hobby extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        protected \Zaproo\Hobby\Model\Hobby $hobby,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getHobby()
    {
        return $this->hobby->getHobby();
    }

    public function getHobbies()
    {
        return $this->hobby->getHobbies();
    }

    public function getFormAction()
    {
        return $this->getUrl('hobby/customer/save');
    }
}