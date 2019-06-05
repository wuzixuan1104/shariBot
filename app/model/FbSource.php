<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

use pimax\Menu\MenuItem;
use pimax\Menu\LocalizedMenu;

class FbSource extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const MENU_VERSION = 1;

  public static function speakerByEvent($event, $bot) {
    if (!$user = $bot->userProfile($event['sender']['id']))
      return null;

    $params = [
      'sid' => $event['sender']['id'],
      'title' => $user->getFirstName() . ' ' . $user->getLastName(),
      'avatar' => $user->getPicture()
    ];

    if (!$source = FbSource::one('sid = ?', $params['sid']))
      if (!transaction(function() use (&$source, $params) { return $source = FbSource::create($params); }))
        return null;

    $source->updateMenu($bot);

    return $source;
  }

  public function updateMenu($bot) {
    if (!($this->token && $this->menuVersion != self::MENU_VERSION))
      return;

    \Log::info('set menu start');
    $bot->deletePersistentMenu();
    $bot->setPersistentMenu([
        new LocalizedMenu('default', false, [
            new MenuItem(MenuItem::TYPE_NESTED, '點我「 查詢訂單 」', [
                new MenuItem(MenuItem::TYPE_POSTBACK, '歷年訂單查詢', json_encode(['order']))
            ])
        ])
    ]);
    \Log::info('set menu end');

    $this->menuVersion = self::MENU_VERSION;
    return $this->save();
  }

  public function getLogModelByEvent($event) {
    $params = [
      'fbSourceId'  => $this->id,
      'timestamp'   => $event['timestamp'],
      'recipientId' => $event['recipient']['id'],
      'senderId'    => $event['sender']['id'],
    ];

    if (isset($event['account_linking'])) {
      $params['status'] = $event['account_linking']['status'];
      $params['authCode'] = isset($event['account_linking']['authorization_code']) ? $event['account_linking']['authorization_code'] : '';
      return \M\transaction(function() use (&$log, $params) { return $log = \M\FbAccountLink::create($params); }) ? $log : null;
    }

    if (isset($event['postback'])) {
      $params['title'] = isset($event['postback']['title']) ? $event['postback']['title'] : '';
      $params['payload'] = $event['postback']['payload'];
      return \M\transaction(function() use (&$log, $params) { return $log = \M\FbPostback::create($params); }) ? $log : null;
    }

    if (isset($event['message'])) {
      $params['mid'] = $event['message']['mid'];
      $params['seq'] = $event['message']['seq'];

      if (isset($event['message']['quick_reply'])) {
        $params['title'] = isset($event['message']['text']) ? $event['message']['text'] : '';
        $params['payload'] = $event['message']['quick_reply']['payload'];
        return \M\transaction(function() use (&$log, $params) { return $log = \M\FbQuick::create($params); }) ? $log : null;
      }

      if (isset($event['message']['text'])) {
        $params['text'] = $event['message']['text'];
        return \M\transaction(function() use (&$log, $params) { return $log = \M\FbText::create($params); }) ? $log : null;
      }

      if (isset($event['message']['attachments'])) {
        $trans = \M\transaction(function() use (&$log, $params, $event) {
          if (!$log = \M\FbAttach::create($params))
            return false;

          foreach ($event['message']['attachments'] as $attach) {
            if (!\M\FbAttachDetail::create(['fbAttachId' => $log->id, 'type' => $attach['type'], 'url' => isset($attach['payload']['url']) ? $attach['payload']['url'] : '', 'payload' => is_array($attach['payload']) ? json_encode($attach['payload']) : '']))
              return false;
          }
          return true;
        });

        return $trans ? $log : null;
      }
    }
    
    return null;
  }
}
