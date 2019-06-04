<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\ImageMessage;

class Fb extends ApiController {
  static $bot = null;
  public $data = [];

  public function __construct() {
    parent::__construct();

    if (Router::methodName() == 'webhook') {
      self::$bot = new FbBotApp(config('fb', 'accessToken'));
      $posts = json_decode(file_get_contents('php://input'), true, 512, JSON_BIGINT_AS_STRING);
      ($this->data = $posts['entry'][0]['messaging']) || error('發生錯誤！');
    }
  }

  public function webhook() {
    foreach ($this->data as $event) {
      Log::info($event);

      if (!(isset($event['message']) || isset($event['postback'])))
        continue;

      $speaker = \M\FbSource::speakerByEvent($event, self::$bot);
      if (!$logModel = $speaker->getLogModelByEvent($event))
        continue;

      switch (get_class($logModel)) {
        case 'M\FbText':
          if ($isSys = $logModel->checkSysTxt())
            continue;

          if (!\M\FbWait::setTimeStamp($speaker))
            self::$bot->send(new Message(\M\FbWait::MSG, $logModel->text));

          
          break;
        case 'M\FbPostback':

          break;
        case 'M\FbAttach':
          self::$bot->send(new ImageMessage($logModel->senderId, $logModel->detail->url));
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