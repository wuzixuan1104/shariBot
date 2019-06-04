<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `FbImageDetail` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `fbImageId`    int(11) unsigned NOT NULL COMMENT '圖片 ID',
    `url`          varchar(50)  COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '圖片連結',
    `payload`      text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Payload 資料',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `FbImageDetail`;",

  'at' => "2019-06-04 11:35:50"
];
