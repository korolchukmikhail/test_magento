<?php

declare(strict_types=1);

namespace Zaproo\Hobby2\Block\Customer;

class Hobby extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        protected \Zaproo\Hobby2\Model\Hobby $hobby,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getUserHobby()
    {
        return $this->hobby->getUserHobby();
    }

    public function getHobbies()
    {
        $hobbies = [];
        foreach ($this->hobby->getHobbies() as $id => $label) {
            $hobbies[] = ['label' => $label, 'value' => $id];
        }

        return $hobbies;
    }

    public function getFormAction()
    {
        return $this->getUrl('customer/hobby/save');
    }
}