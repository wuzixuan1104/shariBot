<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbAttachDetail extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  static $belongToOne = [
    'attach' => 'FbAttach',
  ];

  // static $belongToMany = [];

  // static $uploaders = [];
}
