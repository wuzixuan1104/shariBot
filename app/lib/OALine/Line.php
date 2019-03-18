<?php

namespace OA\Line;

defined('MAPLE') || exit('此檔案不允許讀取！');

class Bot {
  private static $instance;
  protected $channelToken, $channelSecret, $curl, $endpointBase = 'https://api.line.me';

  public static function instance() {
    return !Bot::$instance ? Bot::$instance = new static() : Bot::$instance;
  }

  public function __construct() {
    $this->channelToken = config('line', 'channel', 'token');
    $this->channelSecret = config('line', 'channel', 'secret');
    $this->curl = new Curl($this->channelToken);
  }
  
  public function getProfile($userId) {
    return $this->curl->get($this->endpointBase . '/v2/bot/profile/' . urlencode($userId));
  }
  
  public function getMessageContent($messageId) {
    return $this->curl->get($this->endpointBase . '/v2/bot/message/' . urlencode($messageId) . '/content');
  }

  public function replyMessage($replyToken, Message $message) {
    $messages = $message->buildMessage();
    return $messages ? $this->curl->post($this->endpointBase . '/v2/bot/message/reply', [
      'replyToken' => $replyToken,
      'messages' => $message->buildMessage(),
    ]) : false;
  }

  public function pushMessage($to, Message $message) {
    $messages = $message->buildMessage();
    
    if(ENVIRONMENT === 'development') 
      die (json_encode($message->buildMessage()));

    return $messages ? $this->curl->post($this->endpointBase . '/v2/bot/message/push', [
      'to' => $to,
      'messages' => $message->buildMessage(),
    ]) : false;
  }

  public function pushesMessage(array $tos, Message $message) {
    $messages = $message->buildMessage();
    return $messages ? $this->curl->post($this->endpointBase . '/v2/bot/message/multicast', [
      'to' => $tos,
      'messages' => $message->buildMessage(),
    ]) : false;
  }

  public function leaveGroup($groupId) {
    return $this->curl->post($this->endpointBase . '/v2/bot/group/' . urlencode($groupId) . '/leave', []);
  }

  public function leaveRoom($roomId) {
    return $this->curl->post($this->endpointBase . '/v2/bot/room/' . urlencode($roomId) . '/leave', []);
  }
  
  public function getGroupMemberProfile($groupId, $userId) {
    return $this->curl->get(sprintf('%s/v2/bot/group/%s/member/%s', $this->endpointBase, urlencode($groupId), urlencode($userId)), []);
  }

  public function getRoomMemberProfile($roomId, $userId) {
    return $this->curl->get(sprintf('%s/v2/bot/room/%s/member/%s', $this->endpointBase, urlencode($roomId), urlencode($userId)), []);
  }
  
  public function getGroupMemberIds($groupId, $start = null) {
    return $this->curl->get(sprintf('%s/v2/bot/group/%s/members/ids', $this->endpointBase, urlencode($groupId)), $start ? ['start' => $start] : []);
  }

  public function getRoomMemberIds($roomId, $start = null) {
    return $this->curl->get(sprintf('%s/v2/bot/room/%s/members/ids', $this->endpointBase, urlencode($roomId)), $start ? ['start' => $start] : []);
  }

  // Rich
  public function getRichMenuList() {
    return $this->curl->get($this->endpointBase . '/v2/bot/richmenu/list');
  }
  
  public function createRichMenu(RichMenu $richMenu) {
    $richMenu && $richMenu = $richMenu->build();
    return $richMenu ? $this->curl->post($this->endpointBase . '/v2/bot/richmenu', $richMenu->build()) : false;
  }

  public function getRichMenu($richMenuId) {
    return $this->curl->get(sprintf('%s/v2/bot/richmenu/%s', $this->endpointBase, urlencode($richMenuId)), []);
  }
  
  public function deleteRichMenu($richMenuId) {
    return $this->curl->delete(sprintf('%s/v2/bot/richmenu/%s', $this->endpointBase, urlencode($richMenuId)));
  }
  
  public function downloadRichMenuImage($richMenuId) {
    return $this->curl->get(sprintf('%s/v2/bot/richmenu/%s/content', $this->endpointBase, urlencode($richMenuId)));
  }

  public function uploadRichMenuImage($richMenuId, $imagePath, $contentType) {
    return $this->httpClient->post(
      sprintf('%s/v2/bot/richmenu/%s/content', $this->endpointBase, urlencode($richMenuId)), [
        '__file' => $imagePath,
        '__type' => $contentType,
      ],
      ["Content-Type: $contentType"]
    );
  }
  

  public function getRichMenuId($userId) {
    return $this->curl->get(sprintf('%s/v2/bot/user/%s/richmenu', $this->endpointBase, urlencode($userId)), []);
  }

  public function linkRichMenu($userId, $richMenuId) {
    return $this->curl->post(sprintf('%s/v2/bot/user/%s/richmenu/%s', $this->endpointBase, urlencode($userId), urlencode($richMenuId)), []);
  }

  public function unlinkRichMenu($userId) {
    return $this->curl->delete(sprintf('%s/v2/bot/user/%s/richmenu', $this->endpointBase, urlencode($userId)));
  }
}

class Curl {
  protected $authHeaders, $userAgentHeader;

  public function __construct($channelToken) {
    $this->authHeaders = ["Authorization: Bearer " . $channelToken];
    $this->userAgentHeader = ['User-Agent: LINE-BotSDK-PHP/' . '3.1.0'];
  }
  
  public function get($url){
    return $this->sendRequest('GET', $url, [], []);
  }

  public function post($url, array $data = [], array $headers = []) {
    $headers = $headers ? $headers : ['Content-Type: application/json; charset=utf-8'];
    return $this->sendRequest('POST', $url, $headers, $data);
  }

  public function delete($url) {
    return $this->sendRequest('DELETE', $url, [], []);
  }

  private function getOptions($method, $headers, $reqBody) {
    $options = [
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_BINARYTRANSFER => true,
      CURLOPT_HEADER => true,
    ];
    if ($method === 'POST') {
      if (is_null($reqBody)) {
        // Rel: https://github.com/line/line-bot-sdk-php/issues/35
        $options[CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
      } else {
        if (isset($reqBody['__file']) && isset($reqBody['__type'])) {
          $options[CURLOPT_PUT] = true;
          $options[CURLOPT_INFILE] = fopen($reqBody['__file'], 'r');
          $options[CURLOPT_INFILESIZE] = filesize($reqBody['__file']);
        } elseif (!empty($reqBody)) {
          $options[CURLOPT_POST] = true;
          $options[CURLOPT_POSTFIELDS] = json_encode($reqBody);
        } else {
          $options[CURLOPT_POST] = true;
          $options[CURLOPT_POSTFIELDS] = $reqBody;
        }
      }
    }
    return $options;
  }

  private function sendRequest($method, $url, array $additionalHeader, $reqBody = null) {
    $headers = array_merge($this->authHeaders, $this->userAgentHeader, $additionalHeader);
    $options = $this->getOptions($method, $headers, $reqBody);

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($errno)
      throw new CurlExecutionException($error);

    $httpStatus = $info['http_code'];
    $responseHeaderSize = $info['header_size'];

    $responseHeaderStr = substr($result, 0, $responseHeaderSize);
    $responseHeaders = [];
    foreach (explode("\r\n", $responseHeaderStr) as $responseHeader) {
      $kv = explode(':', $responseHeader, 2);
      count($kv) === 2 && $responseHeaders[$kv[0]] = trim($kv[1]);
    }

    $body = substr($result, $responseHeaderSize);

    isset($options[CURLOPT_INFILE]) && fclose($options[CURLOPT_INFILE]);

    $obj = new \stdClass();
    $obj->status = $httpStatus;
    $obj->body = $body;
    $obj->headers = $responseHeaders;
    $obj->isSucceeded = $httpStatus === 200;
    $obj->jsonBody = json_decode($body, true);
    return $obj;
  }
}

abstract class Event {
  const EVENT_TYPE_CLASS = [
    'follow'   => '\\OA\\Line\\Event\\Follow',
    'unfollow' => '\\OA\\Line\\Event\\Unfollow',
    'join'     => '\\OA\\Line\\Event\\Join',
    'leave'    => '\\OA\\Line\\Event\\Leave',
    'postback' => '\\OA\\Line\\Event\\Postback',
    'beacon'   => '\\OA\\Line\\Event\\BeaconDetection',
    'message'  => '\\OA\\Line\\Event\\Message',
    'accountLink' => '\\OA\\Line\\Event\\AccountLink',
  ];

  const MESSAGE_TYPE_CLASS = [
    'text'     => '\\OA\\Line\\Event\\Text',
    'image'    => '\\OA\\Line\\Event\\Image',
    'video'    => '\\OA\\Line\\Event\\Video',
    'audio'    => '\\OA\\Line\\Event\\Audio',
    'file'     => '\\OA\\Line\\Event\\File',
    'location' => '\\OA\\Line\\Event\\Location',
    'sticker'  => '\\OA\\Line\\Event\\Sticker',
  ];

  public static function all() {
    $events = [];

    if (empty($_SERVER['HTTP_X_LINE_SIGNATURE']))
      return $events;

    $body = file_get_contents("php://input");
    \Log::info($body);

    if (ENVIRONMENT !== 'development' && !hash_equals(base64_encode(hash_hmac('sha256', $body, config('line', 'channel', 'secret'), true)), $_SERVER['HTTP_X_LINE_SIGNATURE']))
      return $events;

    $parsedReq = json_decode($body, true);

    if (!array_key_exists('events', $parsedReq))
      return $events;
    
    \Load::lib('OALine/Event.php');

    foreach ($parsedReq['events'] as $eventData) {

      $eventType = $eventData['type'];

      if (!array_key_exists($eventType, self::EVENT_TYPE_CLASS)) {
        array_push($events, new Event\Unknown($eventData));
        continue;
      }

      $eventClass = self::EVENT_TYPE_CLASS[$eventType];

      if ($eventType === 'message') {

        $messageType = $eventData['message']['type'];
        if (!array_key_exists($messageType, self::MESSAGE_TYPE_CLASS)) {
          array_push($events, new Event\MessageUnknown($eventData));
          continue;
        }

        $messageClass = self::MESSAGE_TYPE_CLASS[$messageType];
        array_push($events, new $messageClass($eventData));
        continue;
      }

      array_push($events, new $eventClass($eventData));
    }

    return $events;
  }

  protected $event;

  public function __construct($event) {
    $this->event = $event;
  }

  public function type() {
    return (string)(array_key_exists('type', $this->event) ? $this->event['type'] : null);
  }
  public function sourceType() {
    return (string)(array_key_exists('source', $this->event) && array_key_exists('type', $this->event['source']) ? $this->event['source']['type'] : null);
  }

  public function timestamp() {
    return (int)(array_key_exists('timestamp', $this->event) ? $this->event['timestamp'] : null);
  }

  public function replyToken() {
    return (string)(array_key_exists('replyToken', $this->event) ? $this->event['replyToken'] : null);
  }

  public function isUserEvent() {
    return $this->sourceType() === \OA\Line\Event\Type::USER;
  }

  public function isGroupEvent() {
    return $this->sourceType() === \OA\Line\Event\Type::GROUP;
  }

  public function isRoomEvent() {
    return $this->sourceType() === \OA\Line\Event\Type::ROOM;
  }

  public function isUnknownEvent() {
    return !($this->isUserEvent() || $this->isGroupEvent() || $this->isRoomEvent());
  }

  public function userId() {
    return (string)(array_key_exists('userId', $this->event['source']) ? $this->event['source']['userId'] : null);
  }

  public function groupId() {
    return (string)(array_key_exists('groupId', $this->event['source']) ? $this->event['source']['groupId'] : null);
  }

  public function roomId() {
    return (string)(array_key_exists('roomId', $this->event['source']) ? $this->event['source']['roomId'] : null);
  }

  public function result() {
    return (string)(array_key_exists('result', $this->event['link']) ? $this->event['link']['result'] : null);
  }

  public function nonce() { 
    return (string)(array_key_exists('nonce', $this->event['link']) ? $this->event['link']['nonce'] : null);
  }

  public function eventSourceId() {
    if ($this->isUserEvent())
        return $this->userId();

    if ($this->isGroupEvent())
        return $this->groupId();

    if ($this->isRoomEvent())
        return $this->roomId();

    return null;
  }
}

abstract class Message {
  protected $quicks = [];

  abstract protected function check();
  abstract public function buildMessage();

  public function pushTo($source) {
    $source instanceof \M\LineSource && isset($source->sid) && $source = $source->sid;
    return !empty($source) ? Bot::instance()->pushMessage($source, $this)->isSucceeded : false;
  }

  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'text'     => '\\OA\\Line\\Message\\TextMessage::create',
      'image'    => '\\OA\\Line\\Message\\ImageMessage::create',
      'video'    => '\\OA\\Line\\Message\\VideoMessage::create',
      'audio'    => '\\OA\\Line\\Message\\AudioMessage::create',
      'sticker'  => '\\OA\\Line\\Message\\StickerMessage::create',
      'location' => '\\OA\\Line\\Message\\LocationMessage::create',
      'imagemap' => '\\OA\\Line\\Message\\ImagemapMessage::create',
      'template' => '\\OA\\Line\\Message\\TemplateMessage::create',
      'flex'     => '\\OA\\Line\\Message\\FlexMessage::create',
    ];

    \Load::lib('OALine/Message.php');

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }

  public function addQuick(Quick $quick = null) {
    $quick && array_push($this->quicks, $quick->buildQuick());
    return $this;
  }

  public function quicks(array $quicks = []) {
    $this->quicks = array_map(function($quick) {
      return $quick instanceof Quick ? $quick->buildQuick() : null;
    }, $quicks);
    return $this;
  }
}

abstract class Quick {

  abstract protected function check();
  abstract public function buildQuick();

  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'action' => '\\OA\\Line\\Message\\ActionQuick::create',
    ];

    \Load::lib('OALine/Message.php');

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }
}