<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbText extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];
  
  const SYSTEM_TEXT = [
    '已點擊「',
    '點我「 ',
  ];

  public function checkSysTxt() {
    foreach (self::SYSTEM_TEXT as $txt) {
      if (strstr($this->text, $txt)) 
        return true;
    }
    return false;
  }
}
