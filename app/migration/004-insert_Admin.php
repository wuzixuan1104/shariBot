<?php defined('MAPLE') || exit('此檔案不允許讀取！');

return [
  'up'   => "INSERT INTO `Admin` (`id`, `name`, `account`, `password`)VALUES(1, '最高管理員', 'root', '$2y$10$7hEPEiJONB/r7ZFZq4d7Wuvn92cRXZwK52vQUDzzyPd7a2grCMBya');",

  'down' => "TRUNCATE TABLE `Admin`;",

  'at'   => "2018-07-30 00:04:09"
];
