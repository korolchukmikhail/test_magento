<?php

declare(strict_types=1);

namespace Zaproo\Hobby\Controller\Customer;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Index implements HttpGetActionInterface
{
    public function __construct(
        protected ResultFactory $resultFactory,
        protected RequestInterface $request,
        protected \Zaproo\Hobby\Model\Hobby $hobby
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setHttpResponseCode(200);
        $result->setData(['hobby' => $this->hobby->getHobbyText()]);

        return $result;
    }
}