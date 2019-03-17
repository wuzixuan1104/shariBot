<?php

// 1. 資料夾名稱
// 2. 副檔名類型
// 3. 是否包含子層
// 4. 是否包含隱藏檔
$_dirs = array (
  ''     => [['html', 'txt'], false, false],
  'js'   => [['js'], true, false],
  'css'  => [['css'], true, false],
  'font' => [['eot', 'svg', 'ttf', 'woff'], true, false],
  'img'  => [['png', 'jpg', 'jpeg', 'gif', 'svg'], true, false],
);