<?php
use Fbbot\App;
use Fbbot\Message;
use Fbbot\El;

Load::lib('fb/Menu.php');
Load::lib('Fbbot.php');

class Postback {
  public static function order() {
    return Menu::order();
  }

  public static function quickSolve($id, $bool, $source) {
    \M\transaction(function() use ($id, $bool) { 
      return \M\FbQuickDetail::create(['fbQuickId' => $id, 'reply' => $bool]); 
    });

    switch($bool) {
      case 0:
        return Message::create()->text('請稍候，客服會盡快為您處理！');
        break;
      case 1:
        return Message::create()->text('很開心解決您的問題！客服就不親自回覆摟：）');
        break;
    }
  }
}