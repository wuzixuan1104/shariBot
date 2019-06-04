<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class FbSource extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const MENU_VERSION = 1.0;

  public static function speakerByEvent($event, $bot) {
    if (!$user = $bot->userProfile($event['sender']['id']))
      return null;

    $params = [
      'sid' => $event['sender']['id'],
      'title' => $user->getFirstName() . ' ' . $user->getLastName(),
    ];

    if (!$source = FbSource::one('sid = ?', $params['sid']))
      if (!transaction(function() use (&$source, $params) { return $source = FbSource::create($params); }))
        return null;

    if ($source->token && $source->menuVersion != self::MENU_VERSION)
      $source->updateMenu();

    return $source;
  }

  public function updateMenu() {

  }

  public function getLogModelByEvent($event) {
    $params = [
      'fbSourceId'  => $this->id,
      'timestamp'   => $event['timestamp'],
      'recipientId' => $event['recipient']['id'],
      'senderId'    => $event['sender']['id'],
    ];

    if (isset($event['message'])) {
      $params['mid'] = $event['message']['mid'] && ($params['seq'] = $event['message']['seq']);
      
      if (isset($event['message']['text'])) {
        $params['text'] = $event['message']['text'];
        return \M\transaction(function() use (&$log, $params) { return $log = \M\FbText::create($params); }) ? $log : null;
      }
    }

  }
}
