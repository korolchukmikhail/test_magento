<?php
class Onilab_TooltipOption_Model_Mysql4_Tooltip extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct(){
        $this->_init('tooltipoption/tooltip', 'tooltip_id');
    }
}