<?php

class ROI_Cin7_Model_Resource_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('roi_cin7/category', 'id');
    }
}