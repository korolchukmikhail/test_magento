<?php
$setup = $this;
$setup->startSetup();

$setup->run("CREATE TABLE {$this->getTable('tooltipoption/tooltip')} (
  `tooltip_id` int(11) unsigned NOT NULL auto_increment COMMENT 'ID',
  `option_id` int(10) unsigned NOT NULL COMMENT 'Bundle ID option' ,
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID' ,
  `tooltip` TEXT NOT NULL COMMENT 'Text/HTML tooltip',
  PRIMARY KEY (`tooltip_id`),
  INDEX `tooltip_option_bundle_IX` (`option_id`),
  INDEX `tooltip_store_bundle_IX` (`store_id`),
  CONSTRAINT `tooltip_option_bundle_FK` FOREIGN KEY (`option_id`) REFERENCES {$this->getTable('bundle/option')}(`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$setup->endSetup();