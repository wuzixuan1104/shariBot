<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\ImageMessage;
use pimax\Messages\SenderAction;

use pimax\Menu\MenuItem;
use pimax\Menu\LocalizedMenu;

class Fb extends ApiController {
  static $bot = null;
  public $data = [];
  public $speaker = null;

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

      $this->speaker = \M\FbSource::speakerByEvent($event, self::$bot);
      if (!$logModel = $this->speaker->getLogModelByEvent($event))
        continue;

      $logClass = get_class($logModel);

      // 檢查是否已綁定會員帳號
      if ($msg = \M\FbAccountLink::check($this->speaker, $logClass)) {
        $this->send($msg);
        continue;
      }

      switch ($logClass) {
        case 'M\FbText':
          if ($logModel->text == 'menu') {
              self::$bot->deletePersistentMenu();
              self::$bot->setPersistentMenu([
                  new LocalizedMenu('default', false, [
                      new MenuItem(MenuItem::TYPE_NESTED, '訂單查詢', [
                          new MenuItem(MenuItem::TYPE_POSTBACK, '點我查詢', json_encode(['order']))
                      ])
                  ])
              ]);

              Log::info('set menu');
              return;
          }
          if ($isSys = $logModel->checkSysTxt())
            continue;

          if (!\M\FbWait::setTimeStamp($this->speaker)) 
            $this->send(new Message($this->speaker->sid, \M\FbWait::MSG));

          break;

        case 'M\FbPostback':
          $params = $logModel->payload;
          if (!($params && ($params = json_decode($params, true))))
            continue;

          $method = array_shift($params);
          
          Load::lib('fb/Postback.php');

          if (!($method && method_exists('Postback', $method))) {
            $this->send(new Message($logModel->senderId, '工程師還沒有設定相對應的功能！'));
            continue;
          }

          array_push($params, $this->speaker);

          if ($msg = call_user_func_array(['Postback', $method], $params)) 
            $this->send($msg);

          break;
        case 'M\FbAttach':
          $this->send(new ImageMessage($logModel->senderId, $logModel->detail->url));
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

  private function send($msg) {
    self::$bot->send(new SenderAction($this->speaker->sid, SenderAction::ACTION_TYPING_ON));
    self::$bot->send($msg);
    self::$bot->send(new SenderAction($this->speaker->sid, SenderAction::ACTION_TYPING_OFF));
  }
}