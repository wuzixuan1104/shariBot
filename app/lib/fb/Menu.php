<?php 

use pimax\Messages\StructuredMessage;
use pimax\Messages\AccountLink;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;

class Menu {
  public static function accountLink($source) {
    return new StructuredMessage($source->sid,
      StructuredMessage::TYPE_GENERIC,
      [
        'elements' => [
          new AccountLink(
            '您好，' . $source->title . '！需要先綁定官方帳號才能使用客服諮詢及訂單查詢功能喔！',
            '(請點選進入官方網站操作登入流程)',
            'https://www.tripresso.com/',
            Url::base('/asset/img/logo.png'))
        ]
      ]
    );
  }

  public static function quickOrder($source) {
    return new QuickReply($source->sid, '', 
      [
        new QuickReplyButton(QuickReplyButton::TYPE_TEXT, '點我「 查詢訂單 」', json_encode(['order'])),
      ]
    );
  }
}