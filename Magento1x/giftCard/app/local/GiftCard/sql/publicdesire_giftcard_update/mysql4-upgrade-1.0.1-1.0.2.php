<?php

$installer = $this;
$installer->startSetup();

function installEmailTemplates()
{
    libxml_use_internal_errors(true);
    try {
        $filename = Mage::getModel('core/config')->getOptions()->getCodeDir() . DS . 'local' . DS . 'PublicDesire'
            . DS . 'GiftCard' . DS . 'sql' . DS . 'publicdesire_giftcard_update' . DS . 'email_templates.xml'
        ;
        $xml = simplexml_load_file($filename, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (!$xml) {
            foreach (libxml_get_errors() as $error) {
                $message = 'Failed to load XML : ' . $error->message;
                Mage::log($message);
            }
            return;
        }

        libxml_clear_errors();

        foreach ($xml as $templateData) {
            $template = Mage::getModel('core/email_template');
            if (!isset($templateData->template_code) || $template->loadByCode((string)$templateData->template_code)->getId()) {
                continue;
            }
            $template
                ->setTemplateSubject((string)$templateData->template_subject)
                ->setTemplateCode((string)$templateData->template_code)
                ->setTemplateText((string)$templateData->template_text)
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setOrigTemplateCode(AW_Giftcard2_Model_Source_Giftcard_Email_Template::DEFAULT_EMAIL_TEMPLATE_PATH)
                ->setAddedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML)
            ;
            $template->save();
        }
    } catch (Exception $e) {
        Mage::logException($e);
    }
}

installEmailTemplates();

$installer->endSetup();
