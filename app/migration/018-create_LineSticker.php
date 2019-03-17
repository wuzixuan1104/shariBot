<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `LineSticker` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `lineSourceId` int(11) unsigned NOT NULL COMMENT 'Source ID',
    `timestamp`    varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '時間',
    `replyToken`   varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回覆 Token',
    `speakerId`    int(11) unsigned NOT NULL COMMENT 'Speaker ID',
    `messageId`    varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '訊息 ID',
    `packageId`    varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Package ID',
    `stickerId`    varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Sticker ID',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `LineSticker`;",

  'at' => "2018-08-14 22:53:27"
];
