<?php

namespace My\Status\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    protected PageFactory $resultPageFactory;
    protected Session $customerSession;
    protected ResultFactory $resultFactory;

    public function __construct(
        PageFactory   $resultPageFactory,
        ResultFactory $resultFactory,
        Session       $customerSession
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->resultFactory = $resultFactory;
    }

    public function execute()
    {
        if(!$this->customerSession->isLoggedIn()){
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('/customer/account/login');

            return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Status'));

        return $resultPage;
    }
}
