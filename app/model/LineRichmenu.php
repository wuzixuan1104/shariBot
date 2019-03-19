<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class LineRichmenu extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const ENABLE_YES = 'yes';
  const ENABLE_NO  = 'no';
  const ENABLE = [
    self::ENABLE_YES => '啟用', 
    self::ENABLE_NO  => '關閉',
  ];

  public static function createNewOne() {
    
  }
}
