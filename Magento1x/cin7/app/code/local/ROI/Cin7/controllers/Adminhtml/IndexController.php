<?php

class ROI_Cin7_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function importAttributeAction()
    {
        /** @var ROI_Cin7_Model_Product $model */
        $model = Mage::getModel('roi_cin7/product');
        $options = $model->getAllOptions();
        $model->updateAttributes($options);

        Mage::getSingleton('adminhtml/session')->addSuccess('All attributes have been updated');
        $this->_redirect('adminhtml/system_config/edit/section/roi_cin7config');
    }

    public function importProductAction()
    {
        /** @var ROI_Cin7_Model_Product $model */
        $model = Mage::getModel('roi_cin7/product');
        $request = $this->getRequest();
        $ids = $request->getParam('ids', []);
        if ($ids) {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $count = $model->updateAllProducts($ids);

        Mage::getSingleton('adminhtml/session')->addSuccess($count . ' products have been updated');
        $this->_redirect('adminhtml/system_config/edit/section/roi_cin7config');
    }

    public function importCategoryAction()
    {
        /** @var ROI_Cin7_Model_Category $model */
        $model = Mage::getModel('roi_cin7/category');
        $model->updateCategories();

        Mage::getSingleton('adminhtml/session')->addSuccess('All categories have been updated');
        $this->_redirect('adminhtml/system_config/edit/section/roi_cin7config');
    }
}