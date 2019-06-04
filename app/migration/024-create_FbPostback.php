<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `FbPostback` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `fbSourceId`   int(11) unsigned NOT NULL COMMENT 'Source ID',
    `timestamp`    varchar(20)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '時間',
    `recipientId`  varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '收件者 ID',
    `senderId`     varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '寄件者 ID',
    `title`        varchar(190)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
    `payload`       text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Action 用',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `FbPostback`;",

  'at' => "2019-06-03 16:52:02"
];
