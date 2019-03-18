<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class LineSource extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  // static $uploaders = [];

  const TYPE_USER    = 'user';
  const TYPE_GROUP   = 'group';
  const TYPE_ROOM    = 'room';
  const TYPE_OTHER   = 'other';
  const TYPE = [
    self::TYPE_USER   => '使用者',
    self::TYPE_GROUP  => '群組',
    self::TYPE_ROOM   => '聊天室',
    self::TYPE_OTHER  => '其他',
  ];

  public static function oneByEvent($event) {
    if (!$sid = $event->eventSourceId())
      return null;
    
    $params = [
      'sid' => $sid,
      'title' => '',
      'type' => array_key_exists($event->sourceType(), self::TYPE) ? $event->sourceType() : self::TYPE_OTHER
    ];

    if (!$source = LineSource::one('sid = ?', $params['sid']))
      if (!transaction(function() use (&$source, $params) { return $source = LineSource::create($params); }))
        return null;

    $source->updateTitle();

    return $source;
  }

  public static function speakerByEvent($event) {
    if (!$userId = ($event->sourceType() == self::TYPE_USER ? $event->eventSourceId() : $event->userId()))
      return null;
    
    $params = [
      'sid' => $userId,
      'title' => '',
      'type' => self::TYPE_USER,
    ];

    if (!$speaker = LineSource::one('sid = ?', $params['sid']))
      if (!transaction(function() use (&$speaker, $params) { return $speaker = LineSource::create($params); }))
        return null;
    
    $speaker->updateTitle();

    return $speaker;
  }

  public function updateTitle() {
    if ($this->type != LineSource::TYPE_USER || $this->title)
      return false;

    if(ENVIRONMENT !== 'development') {
      \Load::lib('OALine/Line.php');
      $response = \OA\Line\Bot::instance()->getProfile($this->sid);

      if (!($response && $response->isSucceeded && array_key_exists('displayName', $response->jsonBody)))
        return false;
      
      $this->title = $response->jsonBody['displayName'];
    } else {
      $this->title = 'OA';
    }
    
    return $this->save();
  }


  private static function getExtensionByMime($m) {
    static $extensions;

    if (isset($extensions[$m]))
      return $extensions[$m];

    foreach (\config('extension') as $ext => $mime)
      if (in_array($m, $mime))
        return $extensions[$m] = '.' . $ext;

    return $extensions[$m] = '';
  }

  private static function putContent($log, $column) {
    if (!(isset($log->id, $log->file, $log->messageId) && !((string)$log->file) && $log->messageId))
      return false;

    \Load::sysFunc('file.php');

    if (!(($response = \OA\Line\Bot::instance()->getMessageContent($log->messageId)) && $response->isSucceeded))
      return false;

    $format = isset($response->headers['Content-Type']) ? self::getExtensionByMime($response->headers['Content-Type']) : '';

    $path = PATH_TMP . uniqid(rand() . '_') . $format;

    if (!fileWrite($path, $response->body))
      return false;

    return $log->{$column}->put($path);
  }

  public function getLogModelByEvent(LineSource $speaker, $event) {
    $params = [
      'lineSourceId'   => $this->id,
      'timestamp'  => $event->timestamp(),
      'file'       => ''
    ];

    in_array($event->type(), ['message', 'postback', 'follow', 'join', 'accountLink']) && $params['replyToken'] = $event->replyToken() ? $event->replyToken() : '';
    in_array($event->type(), ['message', 'postback']) && $params['speakerId'] = $speaker ? $speaker->id : 0;
    in_array($event->type(), ['message']) && $params['messageId'] = $event->messageId()  ? $event->messageId()  : '';

    switch (true) {
      case $event instanceof \OA\Line\Event\Text:
        $params['text'] = $event->text();
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LineText::create($params); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Image:
        return \M\transaction(function() use (&$log, $params) { return ($log = \M\LineImage::create($params)) && self::putContent($log, 'file'); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Audio:
        return \M\transaction(function() use (&$log, $params) {  return ($log = \M\LineAudio::create($params)) && self::putContent($log, 'file'); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Video:
        return \M\transaction(function() use (&$log, $params) { return ($log = \M\LineVideo::create($params)) && self::putContent($log, 'file'); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Follow:
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LineFollow::create($params); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Unfollow:
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LineUnfollow::create($params); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Join:
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LineJoin::create($params); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Leave:
        return \M\transaction(function() use (&$log, $params) {  return $log = \M\LineLeave::create($params); }) ? $log : null;

      case $event instanceof \OA\Line\Event\File:
        $params['title'] = $event->fileName();
        $params['size']  = $event->fileSize();
        return \M\transaction(function() use (&$log, $params) { return ($log = \M\LineFile::create($params)) && self::putContent($log, 'file'); }) ? $log : null;
      
      case $event instanceof \OA\Line\Event\Location:
        $params['title'] = $event->title();
        $params['address'] = $event->address();
        $params['latitude'] = $event->latitude();
        $params['longitude'] = $event->longitude();
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LineLocation::create($params); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Sticker:
        $params['packageId'] = $event->packageId();
        $params['stickerId'] = $event->stickerId();
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LineSticker::create($params); }) ? $log : null;

      case $event instanceof \OA\Line\Event\Postback:
        $params['data'] = json_encode($event->postbackData());
        $params['params'] = json_encode($event->postbackParams());
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LinePostback::create($params); }) ? $log : null;
      case $event instanceof \OA\Line\Event\AccountLink:
        $params['result'] = $event->result();
        $params['nonce'] = $event->nonce();
        return \M\transaction(function() use (&$log, $params) { return $log = \M\LineAccountLink::create($params); }) ? $log : null;
    }
  }
}
