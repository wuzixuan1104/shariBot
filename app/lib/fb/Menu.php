<?php 

use pimax\Messages\StructuredMessage;
use pimax\Messages\AccountLink;

class Menu {
  public static function accountLink($source) {
    return new StructuredMessage($source->sid,
      StructuredMessage::TYPE_GENERIC,
      [
        'elements' => [
          new AccountLink(
            '您好，' . $source->title . '，綁定官方帳號才可以使用客服諮詢及訂單查詢功能喔！',
            '(請點選連結進入官方網站進行登入流程)',
            'https://www.tripresso.com/',
            'https://dszfbyatv8d2t.cloudfront.net/img/logo.svg')
        ]
      ]
    );
  }
}