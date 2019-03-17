<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up'   => "INSERT INTO `AdminRole` (`id`, `adminId`, `role`)VALUES(1, 1, 'root');",

  'down' => "TRUNCATE TABLE `AdminRole`;",

  'at'   => "2018-07-30 00:05:09"
];
