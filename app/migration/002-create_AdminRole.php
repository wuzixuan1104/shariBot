<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `AdminRole` (
    `id`        int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `adminId`   int(11) unsigned NOT NULL COMMENT 'Admin ID',
    `role`      enum('root', 'admin') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '角色',
    `updateAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE IF EXISTS `AdminRole`;",

  'at' => "2018-07-30 00:02:09"
];
