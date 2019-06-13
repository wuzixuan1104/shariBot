<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use Fbbot\App;
use Fbbot\Message;

Load::lib('Fbbot.php');
Load::lib('fb/Menu.php');

class Fb extends ApiController {
  public function __construct() {
    parent::__construct();
  }

  public function webhook() {
    foreach (App::events() as $event) {

      $speaker = \M\FbSource::speakerByEvent($event);
      if (!$logModel = $speaker->getLogModelByEvent($event))
        continue;

      $logClass = get_class($logModel);

      // 檢查是否已綁定會員帳號
      if ($msg = \M\FbAccountLink::check($speaker, $logClass)) {
        $msg->push($speaker);
        continue;
      }

      switch ($logClass) {
        case 'M\FbText':
          if ($isSys = $logModel->checkSysTxt())
            continue;

          if (!\M\FbWait::setTimeStamp($speaker)) 
            Message::create()->text(\M\FbWait::MSG)->push($speaker);

          if (strstr($logModel->text, '訂單') && $msg = Menu::quickOrder($speaker))
            $msg->push($speaker);

          break;

        case 'M\FbPostback':
          $params = $logModel->payload;
          if (!($params && ($params = json_decode($params, true))))
            continue;

          $method = array_shift($params);

          Load::lib('fb/Postback.php');
          if (!($method && method_exists('Postback', $method))) {
            Message::create()->text('工程師還沒有設定相對應的功能！')->push($speaker);
            continue;
          }

          if ($msg = call_user_func_array(['Postback', $method], $params))
            $msg->push($speaker);


          break;
        case 'M\FbAttach':
          Message::create()->image($logModel->detail->url)->push($speaker);
          break;
      }
    }
  }

  public function verify() {
    $gets = Input::get();

    $mode = $gets['hub_mode'];
    $token = $gets['hub_verify_token'];
    $challenge = $gets['hub_challenge'];

    if ($mode && $token) {
      if ($mode === 'subscribe' && $token === config('fb', 'verifyToken')) {
        Log::info('verify success !');
        echo $challenge;
      } else {
        Log::info('verify failed !'); 
      }
    }
  }
}