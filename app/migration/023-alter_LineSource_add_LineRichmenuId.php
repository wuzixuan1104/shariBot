<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "ALTER TABLE `LineSource` ADD `lineRichmenuId` int(11) unsigned NOT NULL COMMENT 'Richmenu ID' AFTER `sid`;",

  'down' => "ALTER TABLE `LineSource` DROP COLUMN `lineRichmenuId`;",

  'at' => "2019-03-19 17:45:35"
];
