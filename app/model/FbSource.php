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
      if (!transaction(function() use (&$source, $params) { return $source = LineSource::create($params); }))
        return null;

    if ($source->token && $source->menuVersion != self::MENU_VERSION)
      $source->updateMenu();

    return $source;
  }

  public function updateMenu() {

  }

  public function getLogModelByEvent($event) {
    $params = [
      'lineSourceId'   => $this->id,
      'timestamp'  => $event->timestamp(),
      'file'       => ''
    ];
  }
}
