<?php

namespace My\Status\Controller\Account;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class Save implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    protected ResultFactory $resultFactory;
    protected MessageManagerInterface $messageManager;
    protected RequestInterface $_request;
    protected Session $customerSession;
    protected Customer $customerResource;
    protected Escaper $escaper;
    protected CustomerFactory $customerFactory;
    protected Validator $formKeyValidator;

    public function __construct(
        Context         $context,
        CustomerFactory $customerFactory,
        Customer        $customerResource,
        Session         $customerSession,
        Escaper         $escaper,
        Validator       $formKeyValidator,
        ResultFactory   $resultFactory
    )
    {
        $this->_request = $context->getRequest();
        $this->resultFactory = $resultFactory;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->customerSession = $customerSession;
        $this->escaper = $escaper;
        $this->formKeyValidator = $formKeyValidator;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Exception
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        if ($this->getRequest()->isPost()) {
            $validFormKey = $this->formKeyValidator->validate($this->getRequest());

            if ($validFormKey) {
                $post = (array)$this->getRequest()->getPost();

                if (!empty($post)) {
                    //get and escape the status
                    $status = (string)$post['status'];
                    $status = $this->escaper->escapeJs($status);
                    $status = $this->escaper->escapeHtml($status);

                    //save the status
                    $customerId = $this->customerSession->getId();
                    if ($customerId) {
                        $customer = $this->customerFactory->create();
                        $this->customerResource->load($customer, $customerId);

                        $customerData = $customer->getDataModel();
                        $customerData->setCustomAttribute(\My\Status\Helper\Definition::ATTR_CODE_STATUS, $status);

                        $customer->updateData($customerData);
                        $this->customerResource->saveAttribute($customer, \My\Status\Helper\Definition::ATTR_CODE_STATUS);

                        $this->messageManager->addSuccessMessage('The Status has been saved!');
                    }
                }
            }
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/mys/account/index');

        return $resultRedirect;
    }

    protected function getRequest(): RequestInterface
    {
        return $this->_request;
    }
}
