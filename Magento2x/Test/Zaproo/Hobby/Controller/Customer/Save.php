<?php

declare(strict_types=1);

namespace Zaproo\Hobby\Controller\Customer;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Save implements HttpPostActionInterface
{
    public function __construct(
        protected ResultFactory $resultFactory,
        protected RequestInterface $request,
        protected \Zaproo\Hobby\Model\Hobby $hobby
    ) {
    }

    public function execute()
    {
        $value = (int)$this->request->getParam('hobby');

        try {
            $this->hobby->saveHobby($value);
        } catch (\Exception $e) {
            //@todo: add error to session for response on front
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setUrl('/hobby/customer/edit');
        
        return $result;
    }
}