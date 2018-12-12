<?php

namespace Test\Status\Controller\Customer;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_statusFactory;
    protected $_resourceModel;
    protected $_customerSession;
    protected $_formKeyValidator;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Test\Status\Model\StatusFactory $statusFactory
    ) {
        $this->_statusFactory = $statusFactory;
        $this->_pageFactory = $pageFactory;
        $this->_formKeyValidator = $formKeyValidator;

        parent::__construct( $context );
    }

    public function execute()
    {
        if ( $this->getRequest()->isPost() ) {
            $validFormKey = $this->_formKeyValidator->validate( $this->getRequest() );

            if ( $validFormKey ) {
                $post = $this->getRequest()->getPostValue();
                $model = $this->_statusFactory->create();

                try {
                    $model->saveStatus( $post['status'] );
                } catch ( \Exception $e ) {
                    //@todo: add error to session for response on front
                }
            }
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->set( __( 'My Status' ) );

        return $resultPage;
    }
}