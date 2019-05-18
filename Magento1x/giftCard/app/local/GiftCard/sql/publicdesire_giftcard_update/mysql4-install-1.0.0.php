<?php

$installer = $this;
$installer->startSetup();


$installer->run("ALTER TABLE {$this->getTable('aw_giftcard2/giftcard')} ADD COLUMN `delivery_date` DATE DEFAULT NULL");

$installer->endSetup();
