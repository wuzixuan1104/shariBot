<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbImage extends Model {
  // static $hasOne = [];

  static $hasMany = [
    'detail' => 'FbImageDetail',
  ];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];
}
