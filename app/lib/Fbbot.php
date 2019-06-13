<?php 
namespace Fbbot;

use \pimax\FbBotApp;

use \pimax\Menu\LocalizedMenu as FbLocalizedMenu;
use \pimax\Menu\MenuItem as FbMenuItem;

use \pimax\Messages\Message as FbMessage;
use \pimax\Messages\ImageMessage as FbImageMessage;
use \pimax\Messages\FileMessage as FbFileMessage;
use \pimax\Messages\AudioMessage as FbAudioMessage;
use \pimax\Messages\VideoMessage as FbVideoMessage;
use \pimax\Messages\SenderAction as FbSenderAction;
use \pimax\Messages\MessageElement as FbMessageElement;
use \pimax\Messages\MessageButton as FbMessageButton;
use \pimax\Messages\StructuredMessage as FbStructuredMessage;
use \pimax\Messages\AccountLink as FbAccountLink;
use \pimax\Messages\QuickReply as FbQuickReply;
use \pimax\Messages\QuickReplyButton as FbQuickReplyButton;

class App extends FbBotApp {
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
      $posts = json_decode(file_get_contents('php://input'), true, 512, JSON_BIGINT_AS_STRING);
      \Log::info($posts['entry'][0]['messaging']);

      return $posts['entry'][0]['messaging'];
    } catch (\Exception $e) {
      return $e;
    }
  }
}

class Message extends FbMessage {
  private $msg = null;

  public function __construct() {
    $this->recipient = null;
  }
  public static function create() {
    return new Message();
  }
  public function pushes($recipient, $objs) {
    !(is_array($objs) && $objs) && error('訊息格式需為陣列！');
    foreach ($objs as $obj) {
      $this->msg = $obj->msg;
      if (!$this->push($recipient))
        return false;
    }
    return true;
  }

  public function push($recipient) {
    if (!$recipient instanceof \M\FbSource)
      return false;

    $this->msg === null && error('送出訊息找不到樣板格式！');
    $this->msg->recipient = $recipient->sid;

    App::bot()->send(new FbSenderAction($recipient->sid, FbSenderAction::ACTION_TYPING_ON));
    $res = App::bot()->send($this->msg);
    App::bot()->send(new FbSenderAction($recipient->sid, FbSenderAction::ACTION_TYPING_OFF));

    isset($res['error']) && error('送出訊息失敗！');
    return true;
  }

  public function text($text, $user_ref = false, $tag = null, $notification_type = self::NOTIFY_REGULAR, $messaging_type = self::TYPE_RESPONSE) {
    $this->msg = new FbMessage($this->recipient, $text, $user_ref, $tag, $notification_type, $messaging_type);
    return $this;
  }
  public function image($file, $quick_replies = [], $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE) {
    $this->msg = new FbImageMessage($this->recipient, $file, $quick_replies, $notification_type, $messaging_type);
    return $this;
  }
  public function file($file, $quick_replies = [], $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE) {
    $this->msg = new FbFileMessage($this->recipient, $file, $quick_replies, $notification_type, $messaging_type);
    return $this;
  }
  public function audio($file, $quick_replies = [], $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE) {
    $this->msg = new FbAudioMessage($this->recipient, $file, $quick_replies = [], $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE);
    return $this;
  }
  public function video($file, $quick_replies = [], $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE) {
    $this->msg = new FbVideoMessage($this->recipient, $file, $quick_replies, $notification_type, $messaging_type);
    return $this;
  }
  public function quick($text, $quick_replies = [], $tag = null, $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE) {
    $this->msg = new FbQuickReply($this->recipient, $text, $quick_replies, $tag, $notification_type, $messaging_type);
    return $this;
  }
  public function struct($type, $data, $top = Struct::TOP_ELEMENT_COMPACT, $quick_replies = [], $tag = null, $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE) {
    $this->msg = new Struct($this->recipient, $type, $data, $quick_replies, $tag, $notification_type, $messaging_type);
    $this->msg->setTopElementStyle($top);
    return $this;
  }
}

class Struct extends FbStructuredMessage {
  const TOP_ELEMENT_TALL = 'tall';
  const TOP_ELEMENT_COMPACT = 'compact';

  public function __construct($recipient, $type, $data, $quick_replies = [], $tag = null, $notification_type = parent::NOTIFY_REGULAR, $messaging_type = parent::TYPE_RESPONSE) {
    parent::__construct($recipient, $type, $data, $quick_replies, $tag, $notification_type, $messaging_type);
  }
  public function setTopElementStyle($value) {
    $this->top_element_style = $value;
    return $this;
  }
}

class ElQickBtn extends FbQuickReplyButton {}
class ElMsgBtn extends FbMessageButton {}
class ElMenuItem extends FbMenuItem {}
class El {
  public static function accountLink($title, $subtitle = '', $url = '', $image_url = '', $logout = false) {
    return new FbAccountLink($title, $subtitle, $url, $image_url, $logout);
  }
  public static function quickBtn($type, $title = '', $payload = null, $image_url = null) {
    return new ElQickBtn($type, $title, $payload, $image_url);
  }
  public static function msgBtn($type, $title = '', $url = '', $webview_height_ratio = '', $messenger_extensions = false, $fallback_url = '', $share_contents = null, $webview_share_button = null) {
    return new ElMsgBtn($type, $title, $url, $webview_height_ratio, $messenger_extensions, $fallback_url, $share_contents, $webview_share_button);
  }
  public static function msgEl($title, $subtitle, $image_url = '', $buttons = [], $url = '', $default_action = []) {
    return new FbMessageElement($title, $subtitle, $image_url, $buttons, $url, $default_action);
  }
  public static function localize($locale, $composer_input_disabled, $menuItems = null) {
    return new FbLocalizedMenu($locale, $composer_input_disabled, $menuItems);
  }
  public static function menuItem($type, $title, $data, $webview_height_ratio = '', $messenger_extensions = false, $fallback_url = '', $webview_share_button =  null) {
    return new ElMenuItem($type, $title, $data, $webview_height_ratio, $messenger_extensions, $fallback_url, $webview_share_button);
  }
}

