<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `FbAttachDetail` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `fbAttachId`   int(11) unsigned NOT NULL COMMENT '附件 ID',
    `type`         enum('image', 'file') NOT NULL DEFAULT 'image' COMMENT '類型',
    `url`          varchar(190)  COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '連結',
    `payload`      text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Payload 資料',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `FbAttachDetail`;",

  'at' => "2019-06-04 11:35:50"
];
