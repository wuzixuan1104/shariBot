<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class LineRichmenu extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const SELECTED_YES = 'yes';
  const SELECTED_NO  = 'no';
  const SELECTED = [
    self::SELECTED_YES => '啟用', 
    self::SELECTED_NO  => '關閉',
  ];

  const ENABLE_YES = 'yes';
  const ENABLE_NO  = 'no';
  const ENABLE = [
    self::ENABLE_YES => '啟用', 
    self::ENABLE_NO  => '關閉',
  ];

  public static function createNewOne($data) {
    $params = [
      'richMenuId' => $data['richMenuId'],
      'name' => $data['name'],
      'chatBarText' => $data['chatBarText'],
      'selected' => $data['selected'] == 1 ? self::SELECTED_YES : self::SELECTED_NO,
      'size' => is_array($data['size']) ? json_encode($data['size']) : $data['size'],
      'area' => is_array($data['areas']) ? json_encode($data['areas']) : $data['areas'],
      'enable' => self::ENABLE_YES,
    ];

    if (!$obj = LineRichmenu::create($params))
      return false;
    
    if ($riches = LineRichmenu::all(['where' => ['id != ? AND enable = ?', $obj->id, self::ENABLE_YES]])) {
      foreach ($riches as $rich) {
        if ( ($rich->enable = self::ENABLE_NO) && !$rich->save())
          return false;
      }
    }
    return true;
  }
}
