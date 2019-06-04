<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `FbWait` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `fbSourceId` int(11) unsigned NOT NULL COMMENT 'Source ID',
    `remindAt`    varchar(20)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '上次提醒傳訊時間',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",


  'down' => "DROP TABLE `FbWait`;",


  'at' => "2019-06-04 15:07:02"
];
