<?php 

use pimax\Messages\StructuredMessage;
use pimax\Messages\AccountLink;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;

use pimax\Messages\MessageElement;
use pimax\Messages\MessageButton;

class Menu {
  public static function accountLink($source) {
    return new StructuredMessage($source->sid, StructuredMessage::TYPE_GENERIC, [
      'elements' => [
        new AccountLink('您好，' . $source->title . '！需要先綁定官方帳號才能使用客服諮詢及訂單查詢功能喔！', '(請點選進入官方網站操作登入流程)', 'https://www.tripresso.com/?type=fb&avatar=' . urlencode($source->avatar), Url::base('/asset/img/logo.png'))
      ]
    ]);
  }

  public static function order($source) {
    return new StructuredMessage($source->sid,
      StructuredMessage::TYPE_LIST,
      [
        'elements' => [
          new MessageElement('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
            new MessageButton(MessageButton::TYPE_POSTBACK, '查看明細', 'POSTBACK')
          ]),
          new MessageElement('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
            new MessageButton(MessageButton::TYPE_POSTBACK, '查看明細', 'POSTBACK')
          ]),
          new MessageElement('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
            new MessageButton(MessageButton::TYPE_POSTBACK, '查看明細', 'POSTBACK')
          ]),
          new MessageElement('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
            new MessageButton(MessageButton::TYPE_POSTBACK, '查看明細', 'POSTBACK')
          ]),
        ],
        'buttons' => [
          new MessageButton(MessageButton::TYPE_POSTBACK, '查看更早之前', 'PAYLOAD 1'),
        ]
      ]
    );
  }

  public static function quickOrder($source) {
    return new QuickReply($source->sid, '系統：點選按鈕快速查詢！', [
      new QuickReplyButton(QuickReplyButton::TYPE_TEXT, '點我「 訂單查詢 」', json_encode(['order'])),
    ]);
  }

  public static function quickSolve($id, $source) {
    return new QuickReply($source->sid, '系統：請問是否已解決您的問題？', [
      new QuickReplyButton(QuickReplyButton::TYPE_TEXT, '是', json_encode(['quickSolve', $id, 1])),
      new QuickReplyButton(QuickReplyButton::TYPE_TEXT, '否', json_encode(['quickSolve', $id, 0])),
    ]);
  }
}