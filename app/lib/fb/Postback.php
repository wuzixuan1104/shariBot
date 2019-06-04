<?php
use pimax\Messages\Message;

Load::lib('fb/Menu.php');

class Postback {
  public static function order($source) {
    return new Message($source->sid, '目前尚無訂單資訊！');
  }
}