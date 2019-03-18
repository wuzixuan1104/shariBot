<?php

namespace OA\Line\Event;
use OA\Line\Event AS Event;

defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class Type {
  const USER = 'user';
  const GROUP = 'group';
  const ROOM = 'room';
}

class Follow extends Event {}
class Unfollow extends Event {}
class Join extends Event {}
class Leave extends Event {}
class AccountLink extends Event {}

class Postback extends Event {
  public function postbackData() {
    return array_key_exists('data', $this->event['postback']) ? json_decode($this->event['postback']['data'], true) : [];
  }
  public function postbackParams() {
    return array_key_exists('params', $this->event['postback']) ? json_decode($this->event['postback']['params'], true) : [];
  }
}

class BeaconDetection extends Event {
  public function hwid() {
    return (string)(array_key_exists('hwid', $this->event['beacon']) ? $this->event['beacon']['hwid'] : null);
  }
  public function beaconEventType() {
    return (string)(array_key_exists('type', $this->event['beacon']) ? $this->event['beacon']['type'] : null);
  }
  public function deviceMessage() {
    return (string)(array_key_exists('dm', $this->event['beacon']) ? pack('H*', $this->event['beacon']['dm']) : null);
  }
}

class Message extends Event {
  protected $message;

  public function __construct($event) {
    parent::__construct($event);
    $this->message = $event['message'];
  }

  public function messageId() {
    return (string)(array_key_exists('id', $this->message) ? $this->message['id'] : null);
  }
  public function messageType() {
    return (string)(array_key_exists('type', $this->message) ? $this->message['type'] : null);
  }
}

class Image extends Message {}
class Video extends Message {}
class Audio extends Message {}
class File extends Message {
  public function fileName() {
    return (string)(array_key_exists('fileName', $this->message) ? $this->message['fileName'] : null);
  }
  public function fileSize() {
    return (string)(array_key_exists('fileSize', $this->message) ? $this->message['fileSize'] : null);
  }
}

class Text extends Message {
  public function text() {
    return (string)(array_key_exists('text', $this->message) ? $this->message['text'] : null);
  }
}

class Location extends Message {
  public function title() {
    return (string)(array_key_exists('title', $this->message) ? $this->message['title'] : null);
  }
  public function address() {
    return (string)(array_key_exists('address', $this->message) ? $this->message['address'] : null);
  }
  public function latitude() {
    return doubleval(array_key_exists('latitude', $this->message) ? $this->message['latitude'] : null);
  }
  public function longitude() {
    return doubleval(array_key_exists('longitude', $this->message) ? $this->message['longitude'] : null);
  }
}

class Sticker extends Message {
  public function packageId() {
    return (string)(array_key_exists('packageId', $this->message) ? $this->message['packageId'] : null);
  }
  public function stickerId() {
    return (string)(array_key_exists('stickerId', $this->message) ? $this->message['stickerId'] : null);
  }
}