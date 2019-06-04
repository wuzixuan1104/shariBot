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

    if (isset($event['postback'])) {
      $params['title'] = isset($event['postback']['title']) ? $event['postback']['title'] : '';
      $params['payload'] = is_array($event['postback']['payload']) ? json_encode($event['postback']['payload']) : '';
      return \M\transaction(function() use (&$log, $params) { return $log = \M\FbPostback::create($params); }) ? $log : null;
    }

    if (isset($event['message'])) {
      \Log::info('type: message');

      $params['mid'] = $event['message']['mid'];
      $params['seq'] = $event['message']['seq'];
      
      if (isset($event['message']['text'])) {
        \Log::info('type: text');
        $params['text'] = $event['message']['text'];
        return \M\transaction(function() use (&$log, $params) { return $log = \M\FbText::create($params); }) ? $log : null;
      }

      if (isset($event['message']['attachments'])) {
        \Log::info('img');
        $trans = \M\transaction(function() use (&$log, $params, $event) {
        \Log::info($params); 
          if (!$obj = \M\FbAttach::create($params))
            return false;

          \Log::info('obj:');
          \Log::info($obj);
          foreach ($event['message']['attachments'] as $attach)
            if (!\M\FbAttachDetail::create(['fbAttachId' => $obj->id, 'type' => $attach['type'], 'url' => isset($attach['payload']['url']) ? $attach['payload']['url'] : '', 'payload' => is_array($attach['payload']) ? json_encode($attach['payload']) : '']))
              return false;
          
          return true;
        });

        return $trans ? $log : null;
      }
    }
    
    return null;
  }
}
