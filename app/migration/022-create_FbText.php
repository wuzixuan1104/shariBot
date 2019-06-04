<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `FbText` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `fbSourceId`   int(11) unsigned NOT NULL COMMENT 'Source ID',
    `timestamp`    varchar(20)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '時間',
    `recipientId`  int(11) unsigned NOT NULL COMMENT '收件人 ID',
    `senderId`     int(11) unsigned NOT NULL COMMENT '寄件人 ID',
    `text`         text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '訊息內容',
    `mid`          varchar(190)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '訊息 ID',
    `seq`          int(11) unsigned NOT NULL COMMENT '序號',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `FbText`;",

  'at' => "2019-06-03 16:51:22"
];
