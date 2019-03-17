<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `Crontab` (
    `id`        int(11) unsigned NOT NULL AUTO_INCREMENT,
    `title`     varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '標題',
    `method`    varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Method Name',
    `params`    text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '參數',
    `status`    enum('failure', 'success') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'failure' COMMENT '狀態',
    `isRead`    enum('yes', 'no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no' COMMENT '已讀',
    `sTime`     varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '開始毫秒',
    `eTime`     varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '結束毫秒',
    `rTime`     varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '耗費毫秒',
    `updateAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE IF EXISTS `Crontab`;",

  'at' => "2018-08-13 11:40:49"
];
