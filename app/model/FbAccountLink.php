<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

use pimax\Messages\Message;

class FbAccountLink extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  public static function check($speaker, $logClass) {
    if ($speaker->token || $logClass == 'M\FbAccountLink') 
      return false;
    
    \Load::lib('fb/Menu.php');

    // if ($linkToken = self::linkToken($speaker)) {
      // $url = config('tripresso', 'web2cUrl') . '?type=line&linkToken=' . $linkToken . '#popup=login';
      return \Menu::accountLink($speaker);
    // } 
    
    return new \Message($speaker->sid, '您的 Line 帳號發生問題，請與客服進一步聯絡！');

  }

  public static function bind($source, $token) {
    
  }
}
