<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `FbQuickDetail` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `fbQuickId`    int(11) unsigned NOT NULL COMMENT '快速回覆 ID',
    `reply`        varchar(255)  COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '回覆內容',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `FbQuickDetail`;",

  'at' => "2019-06-04 22:49:20"
];
