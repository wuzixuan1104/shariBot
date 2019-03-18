<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use \OA\Line\Bot as Bot;
use \OA\Line\Event as Event;
use \OA\Line\Curl as Curl;
use \OA\Line\Message as Message;

class Line extends ApiController {

  public function index() {

    Load::lib('OALine/Line.php');
    
    foreach (Event::all() as $event) {

      if (!$source = \M\LineSource::oneByEvent($event))
        continue;

      $speaker = \M\LineSource::speakerByEvent($event);

      if (!$logModel = $source->getLogModelByEvent($speaker, $event))
        continue;

      switch (get_class($logModel)) {
        case 'M\LineText':
          Load::lib('Menu.php');
          if ($logModel->text == 'orderInfo') {
            $msg = Menu::orderInfo();
            $msg->pushTo($speaker);
          } 
          if ($logModel->text == 'link') {
            $obj = new Curl(config('line', 'channel', 'token'));
            $obj = $obj->post('https://api.line.me/v2/bot/user/' . config('line', 'userId') . '/linkToken');
            $token = $obj->jsonBody['linkToken'];
            return Message::text()->text('http://dev.shari.web.tw/admin/login?linkToken=' . $token)->pushTo($speaker);
          }
          break;

        case 'M\LinePostback':
          Load::lib('Postback.php');
          $params = $logModel->data();
          $method = array_shift($params);

          if (!($method && method_exists('Postback', $method)))
            return Message::text()->text('工程師還沒有設定相對應的功能！')->pushTo($speaker);

          if ($msg = call_user_func_array(['Postback', $method], $params))
            if ($msg instanceof Message)
              return $msg->pushTo($speaker);

          break;
        
        default:
          # code...
          break;
      }
    }
  }
}
