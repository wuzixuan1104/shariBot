<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class LinePostback extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  public function data() {
    if (!isset($this->data))
      return [];

    $data = $this->data;
    return isJson($data) ? $data : [];
  }
}
