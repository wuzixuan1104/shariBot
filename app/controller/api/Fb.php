<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use pimax\FbBotApp;

class Fb extends ApiController {
  static $bot = null;
  public $data = [];

  public function __construct() {
    parent::__construct();

    if (Router::methodName() == 'webhook') {
      self::$bot = new FbBotApp(config('fb', 'accessToken'));
      $this->data = json_decode(file_get_contents('php://input'), true);
      $this->data || error('找不到資料！');

      Log::info(json_encode($this->data));
    }
  }

  public function webhook() {
    foreach ($this->data['entry'] as $entry) {
      foreach ($entry['messaging'] as $event) {
        if (!(isset($event['message']) || isset($event['postback'])))
          continue;

        $speaker = \M\FbSource::speakerByEvent($event, self::$bot);

        // if (!$logModel = $speaker->getLogModelByEvent($event))
        //   continue;

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