<?php

$installer = $this;
$installer->startSetup();


$installer->run("ALTER TABLE {$this->getTable('aw_giftcard2/giftcard')} ADD COLUMN `sent` SMALLINT DEFAULT 0");

$installer->endSetup();
