<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class AdminAction extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const TYPE_READ  = 'read';
  const TYPE_WRITE = 'write';
  const TYPE_OTHER = 'other';
  const TYPE = [
    self::TYPE_READ  => '讀取', 
    self::TYPE_WRITE => '寫入',
    self::TYPE_OTHER => '其他',
  ];

  public static function log($title, $content = '', $type = null) {
    if (!Admin::current())
      return false;

    $adminId = Admin::current()->id;
    isset(self::TYPE[$type]) || $type = self::TYPE_OTHER;

    return self::create([
      'adminId' => $adminId,
      'title' => $title,
      'content' => $content,
      'type' => $type,
    ]);
  }

  public static function read($title, $content = '') {
    return self::log($title, $content, self::TYPE_READ);
  }

  public static function write($title, $content = '') {
    return self::log($title, $content, self::TYPE_WRITE);
  }
}
