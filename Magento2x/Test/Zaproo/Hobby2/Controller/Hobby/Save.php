<?php

declare(strict_types=1);

namespace Zaproo\Hobby2\Controller\Hobby;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Save implements HttpPostActionInterface
{
    public function __construct(
        protected ResultFactory $resultFactory,
        protected RequestInterface $request,
        protected \Zaproo\Hobby2\Model\Hobby $hobby
    ) {
    }

    public function execute()
    {
        $value = (int)$this->request->getParam('hobby');

        try {
            $this->hobby->saveUserHobby($value);
        } catch (\Exception $e) {
            //@todo: add error to session for response on front
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setHttpResponseCode(200);
        $result->setData(['hobby' => $this->hobby->getUserHobbyText()]);
        
        return $result;
    }
}