<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

// use pimax\Messages\Message;
use Fbbot\Message;

\Load::lib('Fbbot.php');
\Load::lib('Curl.php');

class FbAccountLink extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const STATUS_LINKED = 'linked';
  const STATUS_UNLINKED = 'unlinked';

  public static function check($speaker, $logClass) {
    if ($speaker->token || $logClass == 'M\FbAccountLink') 
      return false;
    
    \Load::lib('fb/Menu.php');

    // if ($linkToken = self::linkToken($speaker)) {
      // $url = config('tripresso', 'web2cUrl') . '?type=line&linkToken=' . $linkToken . '#popup=login';
      return \Menu::accountLink($speaker);
    // } 
    
    return Message::create()->text('您的 Line 帳號發生問題，請與客服進一步聯絡！');
    // return new \Message($speaker->sid, '您的 Line 帳號發生問題，請與客服進一步聯絡！');

  }

  public static function bind($source, $token, $bot) {
    if (!$avatar = self::pictureUrl($source))
      $avatar = '';

    \Log::info('LineAccountLink Token: ' . $token);

    \Load::lib('Curl.php');

    $curl = new \Curl();
    $resp = $curl->post(config('tripresso', 'crmUrl') . '/api/chat/bind', [
      'type' => 'line',
      'token' => $token,
      'avatar' => $avatar
    ], ['Content-Type: application/x-www-form-urlencoded']);

    if ($resp->status !== 200)
        return false;

    if ($source->bindToken($token))
      $source->updateMenu($bot);
  }
}
