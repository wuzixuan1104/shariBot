<?php 

use Fbbot\App;
use Fbbot\Message;
use Fbbot\El;
use Fbbot\ElMsgBtn;
use Fbbot\ElQickBtn;
use Fbbot\Struct;

Load::lib('Fbbot.php');

class Menu {
  public static function accountLink($source) {
    return Message::create()->struct(Struct::TYPE_GENERIC, [
      'elements' => [
        El::accountLink('您好，' . $source->title . '！需要先綁定官方帳號才能使用客服諮詢及訂單查詢功能喔！', '(請點選進入官方網站操作登入流程)', 'https://trip.web.shari.tw/admin/login/?type=fb', Url::base('/asset/img/logo.png')),
      ],
    ]);
  }

  public static function order() {
    return Message::create()->struct(Struct::TYPE_LIST, [
      'elements' => [
        El::msgEl('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
          El::msgBtn(ElMsgBtn::TYPE_POSTBACK, '查看明細', 'POSTBACK')
        ]),
        El::msgEl('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
          El::msgBtn(ElMsgBtn::TYPE_POSTBACK, '查看明細', 'POSTBACK')
        ]),
        El::msgEl('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
          El::msgBtn(ElMsgBtn::TYPE_POSTBACK, '查看明細', 'POSTBACK')
        ]),
        El::msgEl('訂單 - 20140922001', '成立日期：2019-01-02 11:23', '', [
          El::msgBtn(ElMsgBtn::TYPE_POSTBACK, '查看明細', 'POSTBACK')
        ]),
      ],
      'buttons' => [
        El::msgBtn(ElMsgBtn::TYPE_POSTBACK, '查看更早之前', 'PAYLOAD 1'),
      ]
    ]);
  }

  public static function quickOrder() {
    return Message::create()->quick('系統：點選按鈕快速查詢！', [
      El::quickBtn(ElQickBtn::TYPE_TEXT, '點我「 訂單查詢 」', json_encode(['order']))
    ]);
  }

  public static function quickSolve($id) {
    return Message::create()->quick('系統：點選按鈕快速查詢！', [
      El::quickBtn(ElQickBtn::TYPE_TEXT, '是', json_encode(['quickSolve', $id, 1])),
      El::quickBtn(ElQickBtn::TYPE_TEXT, '否', json_encode(['quickSolve', $id, 0]))
    ]);
  }
}