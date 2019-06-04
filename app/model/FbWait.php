<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbWait extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  static $belongToOne = [
    'source' => 'LineSource',
  ];

  // static $belongToMany = [];

  // static $uploaders = [];

  const MSG = '系統已收到您的訊息，請稍候客服將立即為您服務！';

  const INTERVAL_TIME = 15;

  public static function setTimeStamp($source) {
    $updRemind = date('Y-m-d H:i:s', strtotime('+' . self::INTERVAL_TIME . ' min'));
    
    if ($obj = \M\FbWait::one(['where' => ['fbSourceId = ?', $source->id]])) {
        $oldRemind = $obj->remindAt;
        $obj->remindAt = $updRemind;

        if ($obj->save() && date('Y-m-d H:i:s') > $oldRemind) 
            return false;
        return true;
    } 

    if (!\M\FbWait::create(['fbSourceId' => $source->id, 'remindAt' => $updRemind])) {
        \Log::error('設定提醒間隔時間失敗：' . $source->id);
        return true;
    }
    return false;
  }
}
