<?php
use pimax\Messages\Message;

class Postback {
  public static function greeting($source) {
    return new Message($source->sid, '請先綁定官方帳號才可以使用以下功能！');
  }

  public static function order($source) {
    return new Message($source->sid, '目前尚無訂單資訊！');
  }
}