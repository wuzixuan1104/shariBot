<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up' => "CREATE TABLE `LineRichmenu` (
    `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
    `richMenuId`   varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Richmenu ID',
    `name`         varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名稱',
    `chatBarText`  varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '下方選單名稱',
    `size`         text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '尺寸',
    `area`         text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '點擊區域',
    `selected`     enum('yes', 'no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no' COMMENT '是否用戶一進來就要顯示選單',
    `enable`       enum('yes', 'no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no' COMMENT '啟用',
    `updateAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    `createAt`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增時間',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

  'down' => "DROP TABLE `LineRichmenu`;",

  'at' => "2019-03-19 17:35:26"
];
