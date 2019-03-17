<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `Admin` (
    `id`        int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name`      varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
    `avatar`    varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '頭像',
    `account`   varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '帳號',
    `password`  varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密碼',
    `updateAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE IF EXISTS `Admin`;",

  'at' => "2018-07-30 00:01:00"
];
