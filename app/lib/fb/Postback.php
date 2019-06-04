<?php
use pimax\Messages\Message;

Load::lib('fb/Menu.php');

class Postback {
  public static function order($logModel, $source) {
    //quickSolve 選單應該在明細的時候才出現，這邊只做測試
    $msg = [];
    array_push($msg, new Message($source->sid, '目前尚無訂單資訊！'));

    if (get_class($logModel) == 'M\FbQuick') 
      array_push($msg, Menu::quickSolve($logModel->id, $source));

    return $msg;
  }

  public static function quickSolve($id, $bool, $source) {
    //若已經解決，則傳訊息至 CRM 告知問題已解決
    Log::info('quickSolve', $id, $bool);
    \M\transaction(function() use ($id, $bool) { 
      return \M\FbQuickDetail::create(['fbQuickId' => $id, 'reply' => $bool]); 
    });
    Log::info('quickSolve success', $id, $bool);
    switch($bool) {
      case 0:
        return new Message($source->sid, '請稍候，客服會盡快為您處理！'); 
        break;
      case 1:
        return new Message($source->sid, '很開心解決您的問題！客服就不親自回覆摟：）'); 
        break;
    }
  }
}