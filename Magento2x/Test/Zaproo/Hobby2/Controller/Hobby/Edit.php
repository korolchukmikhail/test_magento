<?php

declare(strict_types=1);

namespace Zaproo\Hobby2\Controller\Hobby;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit implements HttpGetActionInterface
{
    public function __construct(
        protected PageFactory $pageFactory,
        protected RequestInterface $request
    ) {
    }

    public function execute()
    {
        return $this->pageFactory->create();
    }
}