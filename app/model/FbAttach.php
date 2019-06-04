<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbAttach extends Model {
  static $hasOne = [
    'detail' => 'FbAttachDetail',
  ];

  static $hasMany = [
    'details' => 'FbAttachDetail',
  ];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];
}
