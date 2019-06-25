<?php
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE {$this->getTable('roi_cin7/category')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cin7_id` int(10) unsigned NOT NULL,
  `magento_id` int(10) unsigned NULL,
  `cin7_parentId` int(10) unsigned NOT NULL,
  `cin7_sort` int(10) unsigned NOT NULL default 0,
  `cin7_isActive` smallint(6) NOT NULL default 0,
  `cin7_name` varchar(255) NOT NULL default '',
  `cin7_image` varchar(255) NOT NULL default '',
  `cin7_description` text NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();