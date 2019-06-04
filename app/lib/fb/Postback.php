<?php
use pimax\Messages\Message;

class Postback {
  public static function order($source) {
    return new Message($logModel->senderId, '目前尚無訂單資訊！');
  }
}