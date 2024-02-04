<?php

declare(strict_types=1);

namespace Zaproo\Hobby2\Controller\Hobby;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Index implements HttpGetActionInterface
{
    public function __construct(
        protected ResultFactory $resultFactory,
        protected RequestInterface $request,
        protected \Zaproo\Hobby2\Model\Hobby $hobby
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setHttpResponseCode(200);
        $result->setData(['hobby' => $this->hobby->getUserHobbyText()]);

        return $result;
    }
}