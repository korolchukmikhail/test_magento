<?php


class PublicDesire_GiftCard_Block_Adminhtml_Giftcard_Edit_Tab_Information extends AW_Giftcard2_Block_Adminhtml_Giftcard_Edit_Tab_Information
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function initForm()
    {
        $form = parent::initForm()->getForm();
        $giftcardModel = Mage::registry('current_giftcard');
        $fieldsetInformation = $form->getElement('fieldsetInfo');;
        $fieldsetInformation->addField('delivery_date', 'date', array(
            'name' => 'delivery_date',
            'label' => $this->__('Delivery Date'),
            'title' => $this->__('Delivery Date'),
            'after_element_html' => '<p class="note"><span>'.$this->__('It does not affect on "Expires After, days"').'</span></p>',
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'style' => 'width:220px'
        ));
        
        $formData = $giftcardModel->getData();
        $form->setValues($formData);

        $this->setForm($form);
        return $this;
    }
}