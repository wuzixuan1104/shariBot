<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbAttach extends Model {
  // static $hasOne = [];

  static $hasMany = [
    'detail' => 'FbAttachDetail',
  ];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];
}
