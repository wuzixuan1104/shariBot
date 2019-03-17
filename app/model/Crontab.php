<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class Crontab extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const STATUS_FAILURE = 'failure';
  const STATUS_SUCCESS = 'success';
  const STATUS = [
    self::STATUS_FAILURE => '失敗', 
    self::STATUS_SUCCESS => '成功',
  ];

  const IS_READ_YES = 'yes';
  const IS_READ_NO  = 'no';
  const IS_READ = [
    self::IS_READ_YES => '已讀', 
    self::IS_READ_NO  => '未讀',
  ];
}
