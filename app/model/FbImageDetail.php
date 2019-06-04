<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbImageDetail extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  static $belongToOne = [
    'image' => 'FbImage',
  ];

  // static $belongToMany = [];

  // static $uploaders = [];
}
