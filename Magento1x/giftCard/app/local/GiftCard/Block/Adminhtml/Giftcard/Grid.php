<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftcard2
 * @version    2.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Giftcard2_Block_Adminhtml_Giftcard_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('giftcardGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('aw_giftcard2/giftcard')->getCollection()
            ->addOrderIncrementId()
            ->addProductName()
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _setCollectionOrder($column)
    {
        parent::_setCollectionOrder($column);
        $collection = $this->getCollection();
        if ($collection) {
            $collection->setOrder('order_id', Varien_Data_Collection::SORT_ORDER_DESC);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'        => $this->__('Created At'),
            'width'         => 50,
            'type'          => 'date',
            'index'         => 'created_at',
            'filter_index'  => 'main_table.created_at',
            'filter'        => 'aw_giftcard2/adminhtml_widget_grid_column_filter_date'
        ));

        $this->addColumn('order', array(
            'header'        => $this->__('Order'),
            'index'         => 'order_increment_id',
            'filter_index' => 'order.increment_id',
            'type'          => 'text',
            'width'         => 50,
            'renderer'      => 'aw_giftcard2/adminhtml_widget_grid_column_renderer_order'
        ));

        $this->addColumn('product', array(
            'header'        => $this->__('Product'),
            'index'         => 'product_name',
            'filter_index'  => 'name_def.value',
            'type'          => 'text',
            'width'         => 120,
            'renderer'      => 'aw_giftcard2/adminhtml_widget_grid_column_renderer_product'
        ));

        $this->addColumn('type', array(
            'header'        => $this->__('Type'),
            'index'         => 'type',
            'type'          => 'options',
            'width'         => 50,
            'options'       => Mage::getModel('aw_giftcard2/source_entity_attribute_giftcard_type')->toArray(),
        ));

        $this->addColumn('code', array(
            'header'        => $this->__('Code'),
            'index'         => 'code',
            'type'          => 'text',
            'width'         => 100
        ));

        $this->addColumn('initial_balance', array(
            'header'        => $this->__('Initial Amount'),
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            'type'          => 'currency',
            'index'         => 'initial_balance',
            'width'         => 50,
            'renderer'      => 'aw_giftcard2/adminhtml_widget_grid_column_renderer_amount'
        ));

        $this->addColumn('availability', array(
            'header'    => $this->__('Availability'),
            'width'     => 100,
            'align'     => 'center',
            'index'     => 'availability',
            'type'      => 'options',
            'options'   => Mage::getModel('aw_giftcard2/source_giftcard_availability')->toArray(),
        ));

        $this->addColumn('is_used', array(
            'header'    => $this->__('Is Used'),
            'width'     => 50,
            'align'     => 'center',
            'index'     => 'is_used',
            'type'      => 'options',
            'options'   => Mage::getModel('aw_giftcard2/source_giftcard_used')->toArray(),
        ));

        $this->addColumn('balance', array(
            'header'        => $this->__('Balance'),
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            'type'          => 'currency',
            'index'         => 'balance',
            'width'         => 50,
            'renderer'      => 'aw_giftcard2/adminhtml_widget_grid_column_renderer_amount'
        ));

        $this->addColumn('expire_at', array(
            'header'  => $this->__('Expiration Date'),
            'width'   => 50,
            'type'    => 'date',
            'index'   => 'expire_at',
            'default' => '--',
            'filter'   => 'aw_giftcard2/adminhtml_widget_grid_column_filter_date'
        ));

        $this->addColumn('recipient_name', array(
            'header'        => $this->__('Recipient Name'),
            'index'         => 'recipient_name',
            'type'          => 'text',
            'width'         => 200,
            'renderer'      => 'aw_giftcard2/adminhtml_widget_grid_column_renderer_recipient'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website',
                array(
                    'header'=> $this->__('Website'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'website_id',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ));
        }

        $this->addColumn('email_template', array(
            'header'        => $this->__('Email Template'),
            'index'         => 'email_template',
            'type'          => 'options',
            'sortable'      => false,
            'width'         => 200,
            'options'       => Mage::getModel('aw_giftcard2/source_giftcard_email_template')->toArray(),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('giftcard');

        $this->getMassactionBlock()->addItem('activate', array(
            'label'=> $this->__('Activate'),
            'url'  => $this->getUrl('*/*/massActivate', array('_current'=>true)),
            'confirm' => $this->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('deactivate', array(
            'label'=> $this->__('Deactivate'),
            'url'  => $this->getUrl('*/*/massDeactivate', array('_current'=>true)),
            'confirm' => $this->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => $this->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => $this->__('Are you sure?')
        ));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getGiftcardId()));
    }
}