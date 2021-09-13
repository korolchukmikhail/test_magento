<?php

namespace My\Status\Block;

class Status extends \Magento\Framework\View\Element\Template
{
    protected \Magento\Customer\Model\Session $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session                  $customerSession,
        array                                            $data = []
    )
    {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getMyStatus(): string
    {
        return _($this->customerSession->getCustomer()->getData(\My\Status\Helper\Definition::ATTR_CODE_STATUS));
    }


    public function getFormAction(): string
    {
        return '/mys/account/save';
    }
}
