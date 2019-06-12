<?php 

use pimax\Messages\StructuredMessage;
use pimax\Messages\AccountLink;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;

use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\ImageMessage;
use pimax\Messages\SenderAction;
use pimax\Messages\MessageElement;
use pimax\Messages\MessageButton;

class ShariBot extends FbBotApp {
    static $bot;

    public function __construct($accessToken) {
      parent::__construct($accessToken);
    }
    public static function create() {
      if (!config('fb', 'accessToken'))
        error('請設定 Config FB accessToken!');

      return new FbBotApp(config('fb', 'accessToken'));
    }
    public static function bot() {
      if (self::$bot)
        return self::$bot;
      return self::$bot = self::create();
    }
    public static function events() {
      try {
        Log::info( file_get_contents ("php://input") );
        
        $params = json_decode(file_get_contents('php://input'), true, 512, JSON_BIGINT_AS_STRING);
        return $posts['entry'][0]['messaging'];
      } catch (Exception $e) {
        return $e;
      }
    }

}