<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `AdminAction` (
    `id`        int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `adminId`   int(11) unsigned NOT NULL COMMENT 'Admin ID',
    `title`     varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
    `content`   text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '內容',
    `type`      enum('read','write','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other' COMMENT '類型',
    `updateAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE IF EXISTS `AdminAction`;",

  'at' => "2018-09-21 11:47:29"
];
