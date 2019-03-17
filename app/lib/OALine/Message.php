<?php

namespace OA\Line\Message;
use OA\Line\Message AS Message;
use OA\Line\Quick AS Quick;

defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class QuickType {
  const ACTION = 'action';
}

abstract class MessageType {
  const TEXT     = 'text';
  const IMAGE    = 'image';
  const VIDEO    = 'video';
  const AUDIO    = 'audio';
  const STICKER  = 'sticker';
  const LOCATION = 'location';
  const IMAGEMAP = 'imagemap';
  const TEMPLATE = 'template';
  const FLEX = 'flex';
}

abstract class ActionType {
  const URI = 'uri';
  const MESSAGE = 'message';
  const POSTBACK = 'postback';
  const DATETIME_PICKER = 'datetimepicker';
  const CAMERA = 'camera';
  const CAMERA_ROLL = 'cameraRoll';
  const LOCATION = 'location';
}

abstract class TemplateType {
  const CONFIRM = 'confirm';
  const BUTTONS = 'buttons';
  const CAROUSEL = 'carousel';
  const IMAGE_CAROUSEL = 'image_carousel';
}

abstract class FlexTemplateType {
  const BUBBLE = 'bubble';
  const CAROUSEL = 'carousel';
}

abstract class Func {
  public static function isTel($url) {
    return preg_match('/^tel:\/\/.*/', $url);
  }
  public static function isHttp($url) {
    return preg_match('/^http:\/\/.*/', $url);
  }
  public static function isHttps($url) {
    return preg_match('/^https:\/\/.*/', $url);
  }
  public static function isHttpsL1000($url) {
    return self::isHttps($url) && mb_strlen($url) <= 1000;
  }
  public static function timeFromat($val) {
    return \DateTime::createFromFormat('H:i', $val) !== false;
  }
  public static function dateFromat($val) {
    return \DateTime::createFromFormat('Y-m-d', $val) !== false;
  }
  public static function datetimeFromat($val) {
    return \DateTime::createFromFormat('Y-m-dtH:i', $val) !== false;
  }
  public static function catStr(&$str, $len) {
    return mb_strlen($str) > $len ? mb_substr($str, 0, $len - 1) . '…' : $str;
  }
}

class TextMessage extends Message {
  protected $texts = [];

  public function __construct() {
    call_user_func_array([$this, 'text'], func_get_args());
  }

  public function text() {
    $this->texts = array_filter(array_map(function($arg) { return is_string($arg) ? trim($arg) : null; }, func_get_args()));
    return $this;
  }

  protected function check() {
    return $this->texts;
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);

    return $this->check() ? array_map(function($text) {
      return [
        'type' => MessageType::TEXT,
        'text' => $text,
        'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
      ];
    }, $this->texts) : null;
  }
}

class ImageMessage extends Message {
  protected $originalContentUrl, $previewImageUrl;

  public function __construct($originalContentUrl = null, $previewImageUrl = null) {
    $this->originalContentUrl($originalContentUrl)
         ->previewImageUrl($previewImageUrl);
  }

  public function originalContentUrl($originalContentUrl) {
    is_string($originalContentUrl) && ($originalContentUrl = trim($originalContentUrl)) && Func::isHttpsL1000($originalContentUrl) && $this->originalContentUrl = $originalContentUrl;
    return $this;
  }

  public function previewImageUrl($previewImageUrl) {
    is_string($previewImageUrl) && ($previewImageUrl = trim($previewImageUrl)) && Func::isHttpsL1000($previewImageUrl) && $this->previewImageUrl = $previewImageUrl;
    return $this;
  }
  
  protected function check() {
    $this->previewImageUrl || $this->previewImageUrl = $this->originalContentUrl;
    return isset($this->originalContentUrl, $this->previewImageUrl);
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);
    
    return $this->check() ? [[
      'type' => MessageType::IMAGE,
      'originalContentUrl' => $this->originalContentUrl,
      'previewImageUrl' => $this->previewImageUrl,
      'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
    ]] : null;
  }
}

class VideoMessage extends Message {
  protected $originalContentUrl, $previewImageUrl;

  public function __construct($originalContentUrl = null, $previewImageUrl = null) {
    $this->originalContentUrl($originalContentUrl)
         ->previewImageUrl($previewImageUrl);
  }

  public function originalContentUrl($originalContentUrl) {
    is_string($originalContentUrl) && ($originalContentUrl = trim($originalContentUrl)) && Func::isHttpsL1000($originalContentUrl) && $this->originalContentUrl = $originalContentUrl;
    return $this;
  }

  public function previewImageUrl($previewImageUrl) {
    is_string($previewImageUrl) && ($previewImageUrl = trim($previewImageUrl)) && Func::isHttpsL1000($previewImageUrl) && $this->previewImageUrl = $previewImageUrl;
    return $this;
  }
  
  protected function check() {
    return isset($this->originalContentUrl, $this->previewImageUrl);
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);
    
    return $this->check() ? [[
      'type' => MessageType::VIDEO,
      'originalContentUrl' => $this->originalContentUrl,
      'previewImageUrl' => $this->previewImageUrl,
      'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
    ]] : null;
  }
}

class AudioMessage extends Message {
  protected $originalContentUrl, $duration;

  public function __construct($originalContentUrl = null, $duration = 0) {
    $this->originalContentUrl($originalContentUrl)
         ->duration($duration);
  }

  public function originalContentUrl($originalContentUrl) {
    is_string($originalContentUrl) && ($originalContentUrl = trim($originalContentUrl)) && Func::isHttpsL1000($originalContentUrl) && $this->originalContentUrl = $originalContentUrl;
    return $this;
  }

  public function duration($duration) {
    is_numeric($duration) && $duration > 0 && $this->duration = (int)$duration;
    return $this;
  }
  
  protected function check() {
    return isset($this->originalContentUrl, $this->duration);
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);
    
    return $this->check() ? [[
      'type' => MessageType::AUDIO,
      'originalContentUrl' => $this->originalContentUrl,
      'duration' => $this->duration,
      'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
    ]] : null;
  }
}

class StickerMessage extends Message {
  protected $packageId, $stickerId;

  public function __construct($packageId = null, $stickerId = null) {
    $this->packageId($packageId)
         ->stickerId($stickerId);
  }

  public function packageId($packageId) {
    (is_string($packageId) || is_numeric($packageId)) && ($packageId = trim($packageId)) && $this->packageId = $packageId;
    return $this;
  }

  public function stickerId($stickerId) {
    (is_string($stickerId) || is_numeric($stickerId)) && ($stickerId = trim($stickerId)) && $this->stickerId = $stickerId;
    return $this;
  }

  protected function check() {
    return isset($this->packageId, $this->stickerId);
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);
    
    return $this->check() ? [[
      'type' => MessageType::STICKER,
      'packageId' => $this->packageId,
      'stickerId' => $this->stickerId,
      'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
    ]] : null;
  }
}

class LocationMessage extends Message {
  protected $title, $address, $latitude, $longitude;

  public function __construct($title = null, $address = null, $latitude = null, $longitude = null) {
    $this->title($title)
         ->address($address)
         ->latitude($latitude)
         ->longitude($longitude);
  }

  public function title($title) {
    is_string($title) && ($title = trim($title)) && ($title = Func::catStr($title, 100)) && $this->title = $title;
    return $this;
  }

  public function address($address) {
    is_string($address) && ($address = trim($address)) && ($address = Func::catStr($address, 100)) && $this->address = $address;
    return $this;
  }

  public function latitude($latitude) {
    is_numeric($latitude) && $latitude >= -90 && $latitude <= 90 && $this->latitude = $latitude;
    return $this;
  }

  public function longitude($longitude) {
    is_numeric($longitude) && $longitude >= -180 && $longitude <= 180 && $this->longitude = $longitude;
    return $this;
  }

  protected function check() {
    return isset($this->title, $this->address, $this->latitude, $this->longitude);
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);
    
    return $this->check() ? [[
      'type' => MessageType::LOCATION,
      'title' => $this->title,
      'address' => $this->address,
      'latitude' => $this->latitude,
      'longitude' => $this->longitude,
      'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
    ]] : null;
  }
}

class ImagemapMessage extends Message {
  protected $baseUrl, $altText, $width, $height, $actions = [];

  public function __construct($baseUrl = null, $altText = null, $width = null, $height = null, array $actions = []) {
    $this->baseUrl($baseUrl)
         ->altText($altText)
         ->width($width)
         ->height($height)
         ->actions($actions);
  }

  public function baseUrl($baseUrl) {
    is_string($baseUrl) && ($baseUrl = trim($baseUrl)) && Func::isHttpsL1000($baseUrl) && $this->baseUrl = $baseUrl;
    return $this;
  }

  public function altText($altText) {
    is_string($altText) && ($altText = trim($altText)) && ($altText = Func::catStr($altText, 400)) && $this->altText = $altText;
    return $this;
  }

  public function width($width) {
    is_numeric($width) && $width > 0 && $this->width = (int)$width;
    return $this;
  }

  public function height($height) {
    is_numeric($height) && $height > 0 && $this->height = (int)$height;
    return $this;
  }

  public function addAction(ImagemapAction $action = null) {
    $action && array_push($this->actions, $action->buildAction());
    return $this;
  }

  public function actions(array $actions = []) {
    $this->actions = array_map(function($action) {
      return $action instanceof ImagemapAction ? $action->buildAction() : null;
    }, $actions);
    return $this;
  }

  protected function check() {
    return isset($this->baseUrl, $this->altText, $this->width, $this->height);
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);
    
    return $this->check() ? [[
      'type' => MessageType::IMAGEMAP,
      'baseUrl' => $this->baseUrl,
      'altText' => $this->altText,
      'baseSize' => [
        'width' => $this->width,
        'height' => $this->height,
      ],
      'actions' => array_slice(array_values(array_filter($this->actions)), -50),
      'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
    ]] : null;
  }
}

class TemplateMessage extends Message {
  protected $altText, $template;
  
  public function __construct($altText = null, Template $template = null) {
    $this->altText($altText)
         ->template($template);
  }

  public function altText($altText) {
    is_string($altText) && ($altText = trim($altText)) && ($altText = Func::catStr($altText, 400)) && $this->altText = $altText;
    return $this;
  }

  public function template(Template $template = null) {
    $template && $template instanceof Template && $this->template = $template->buildTemplate();
    return $this;
  }
  
  protected function check() {
    return isset($this->altText, $this->template);
  }

  public function buildMessage() {
    $this->quicks = array_slice(array_values(array_filter($this->quicks)), -13);
    
    return $this->check() ? [[
      'type' => MessageType::TEMPLATE,
      'altText' => $this->altText,
      'template' => $this->template,
      'quickReply' => $this->quicks ? ['items' => $this->quicks] : null
    ]] : null;
  }
}

class FlexMessage extends Message {
  protected $altText, $contents;
  

  public function __construct($altText = null, FlexTemplate $template = null) {
    $this->altText($altText);
  }

  public function altText($altText) {
    is_string($altText) && ($altText = trim($altText)) && ($altText = Func::catStr($altText, 400)) && $this->altText = $altText;
    return $this;
  }
  public function contents($contents) {
    $this->contents = $contents;
    return $this;
  }

  protected function check() {
    return isset($this->altText, $this->template);
  }

  public function template(FlexTemplate $template = null) {
    $template && $template instanceof FlexTemplate && $this->template = $template->buildTemplate();
    return $this;
  }

  public function buildMessage() {
    return $this->check() ? [[
      'type' => MessageType::FLEX,
      'altText' => $this->altText,
      "contents" => $this->template
    ]] : null;
  }
}

abstract class Action {
  abstract protected function check();
  abstract public function buildAction();
}

abstract class ImagemapAction extends Action {
  protected $x, $y, $width, $height, $label;

  protected function area() {
    return [
      'x' => $this->x,
      'y' => $this->y,
      'width' => $this->width,
      'height' => $this->height,
    ];
  }

  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'url'     => '\\OA\\Line\\Message\\ImagemapUriAction::create',
      'message' => '\\OA\\Line\\Message\\ImagemapMessageAction::create',
    ];

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }

  public function x($x) {
    is_numeric($x) && $x >= 0 && $this->x = (int)$x;
    return $this;
  }

  public function y($y) {
    is_numeric($y) && $y >= 0 && $this->y = (int)$y;
    return $this;
  }

  public function width($width) {
    is_numeric($width) && $width > 0 && $this->width = (int)$width;
    return $this;
  }

  public function height($height) {
    is_numeric($height) && $height > 0 && $this->height = (int)$height;
    return $this;
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 50)) && $this->label = $label;
    return $this;
  }

  protected function check() {
    return isset($this->x, $this->y, $this->width, $this->height);
  }
}

abstract class TemplateAction extends Action {
  protected $label;
  
  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'uri'        => '\\OA\\Line\\Message\\TemplateUriAction::create',
      'message'    => '\\OA\\Line\\Message\\TemplateMessageAction::create',
      'postback'   => '\\OA\\Line\\Message\\TemplatePostbackAction::create',
      'time'       => '\\OA\\Line\\Message\\TemplateTimeAction::create',
      'date'       => '\\OA\\Line\\Message\\TemplateDateAction::create',
      'datetime'   => '\\OA\\Line\\Message\\TemplateDatetimeAction::create',
      'camera'     => '\\OA\\Line\\Message\\TemplateCameraAction::create',
      'cameraRoll' => '\\OA\\Line\\Message\\TemplateCameraRollAction::create',
      'location'   => '\\OA\\Line\\Message\\TemplateLocationAction::create',
    ];

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }

  abstract public function label($label);
}

abstract class TemplateDatetimePickerAction extends TemplateAction {
  protected $label, $data = [], $mode, $initial, $max, $min;

  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null, $mode = null) {
    $this->mode($mode)
         ->data($data)
         ->label($label)
         ->initial($initial)
         ->max($max)
         ->min($min);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  public function data(array $data = []) {
    $data = json_encode($data);
    $data && mb_strlen($data) <= 300 && $this->data = $data;
    return $this;
  }

  public function mode($mode) {
    is_string($mode) && ($mode = trim($mode)) && in_array($mode, ['date', 'time', 'datetime']) && $this->mode = $mode;
    return $this;
  }

  public function initial($initial) {
    $this->initial = $initial;
    return $this;
  }

  public function max($max) {
    $this->max = $max;
    return $this;
  }

  public function min($min) {
    $this->min = $min;
    return $this;
  }

  public function check() {
    return isset($this->label, $this->mode) && $this->data;
  }

  public function buildAction() {
    $return = [
      'type' => ActionType::DATETIME_PICKER,
      'label' => $this->label,
      'data' => $this->data,
      'mode' => $this->mode,
    ];

    switch ($this->mode) {
      case 'time':
        $this->initial && Func::timeFromat($this->initial)     && $return['initial'] = $this->initial;
        $this->max     && Func::timeFromat($this->max)         && $return['max']     = $this->max;
        $this->min     && Func::timeFromat($this->min)         && $return['min']     = $this->min;
        break;

      case 'date':
        $this->initial && Func::dateFromat($this->initial)     && $return['initial'] = $this->initial;
        $this->max     && Func::dateFromat($this->max)         && $return['max']     = $this->max;
        $this->min     && Func::dateFromat($this->min)         && $return['min']     = $this->min;
        break;

      case 'datetime':
        $this->initial && Func::datetimeFromat($this->initial) && $return['initial'] = $this->initial;
        $this->max     && Func::datetimeFromat($this->max)     && $return['max']     = $this->max;
        $this->min     && Func::datetimeFromat($this->min)     && $return['min']     = $this->min;
        break;
    }

    return $this->check() ? $return : null;
  }
}

abstract class QuickAction extends Action {
  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'postback'   => '\\OA\\Line\\Message\\QuickPostbackAction::create',
      'message'    => '\\OA\\Line\\Message\\QuickMessageAction::create',
      'time'       => '\\OA\\Line\\Message\\QuickTimeAction::create',
      'date'       => '\\OA\\Line\\Message\\QuickDateAction::create',
      'datetime'   => '\\OA\\Line\\Message\\QuickDatetimeAction::create',
      'camera'     => '\\OA\\Line\\Message\\QuickCameraAction::create',
      'cameraRoll' => '\\OA\\Line\\Message\\QuickCameraRollAction::create',
      'location'   => '\\OA\\Line\\Message\\QuickLocationAction::create',
    ];

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }
}

abstract class QuickDatetimePickerAction extends QuickAction {
  protected $label, $data = [], $mode, $initial, $max, $min;

  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null, $mode = null) {
    $this->mode($mode)
         ->data($data)
         ->label($label)
         ->initial($initial)
         ->max($max)
         ->min($min);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  public function data(array $data = []) {
    $data = json_encode($data);
    $data && mb_strlen($data) <= 300 && $this->data = $data;
    return $this;
  }

  public function mode($mode) {
    is_string($mode) && ($mode = trim($mode)) && in_array($mode, ['date', 'time', 'datetime']) && $this->mode = $mode;
    return $this;
  }

  public function initial($initial) {
    $this->initial = $initial;
    return $this;
  }

  public function max($max) {
    $this->max = $max;
    return $this;
  }

  public function min($min) {
    $this->min = $min;
    return $this;
  }

  public function check() {
    return isset($this->label, $this->mode) && $this->data;
  }

  public function buildAction() {
    $return = [
      'type' => ActionType::DATETIME_PICKER,
      'label' => $this->label,
      'data' => $this->data,
      'mode' => $this->mode,
    ];

    switch ($this->mode) {
      case 'time':
        $this->initial && Func::timeFromat($this->initial)     && $return['initial'] = $this->initial;
        $this->max     && Func::timeFromat($this->max)         && $return['max']     = $this->max;
        $this->min     && Func::timeFromat($this->min)         && $return['min']     = $this->min;
        break;

      case 'date':
        $this->initial && Func::dateFromat($this->initial)     && $return['initial'] = $this->initial;
        $this->max     && Func::dateFromat($this->max)         && $return['max']     = $this->max;
        $this->min     && Func::dateFromat($this->min)         && $return['min']     = $this->min;
        break;

      case 'datetime':
        $this->initial && Func::datetimeFromat($this->initial) && $return['initial'] = $this->initial;
        $this->max     && Func::datetimeFromat($this->max)     && $return['max']     = $this->max;
        $this->min     && Func::datetimeFromat($this->min)     && $return['min']     = $this->min;
        break;
    }

    return $this->check() ? $return : null;
  }
}

class TemplateUriAction extends TemplateAction {
  protected $uri;

  public function __construct($label = null, $uri = null) {
    $this->label($label)->uri($uri);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  public function uri($uri) {
    is_string($uri) && ($uri = trim($uri)) && (Func::isHttps($uri) || Func::isHttp($uri) || Func::isTel($uri)) && mb_strlen($uri) <= 1000 && $this->uri = $uri;
    return $this;
  }
  
  protected function check() {
    return isset($this->uri, $this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::URI,
      'label' => $this->label,
      'uri' => $this->uri,
    ] : null;
  }
}

class TemplateMessageAction extends TemplateAction {
  protected $text;

  public function __construct($label = null, $text = null) {
    $this->label($label)->text($text);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, 300)) && $this->text = $text;
    return $this;
  }
  
  protected function check() {
    return isset($this->text, $this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::MESSAGE,
      'label' => $this->label,
      'text' => $this->text,
    ] : null;
  }
}

class TemplatePostbackAction extends TemplateAction {
  protected $label, $text, $data = [];

  public function __construct($label = null, array $data = [], $text = null) {
    $this->label($label)
         ->data($data)
         ->text($text);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, 300)) && $this->text = $text;
    return $this;
  }

  public function data(array $data = []) {
    $data = json_encode($data);
    $data && mb_strlen($data) <= 300 && $this->data = $data;
    return $this;
  }

  protected function check() {
    return isset($this->label) && $this->data;
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::POSTBACK,
      'label' => $this->label,
      'data' => $this->data,
      'displayText' => $this->text,
    ] : null;
  }
}

class TemplateCameraAction extends TemplateAction {
  protected $label;

  public function __construct($label = null) {
    $this->label($label);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  protected function check() {
    return isset($this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::CAMERA,
      'label' => $this->label,
    ] : null;
  }
}

class TemplateCameraRollAction extends TemplateAction {
  protected $label;

  public function __construct($label = null) {
    $this->label($label);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  protected function check() {
    return isset($this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::CAMERA_ROLL,
      'label' => $this->label,
    ] : null;
  }
}

class TemplateLocationAction extends TemplateAction {
  protected $label;

  public function __construct($label = null) {
    $this->label($label);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  protected function check() {
    return isset($this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::LOCATION,
      'label' => $this->label,
    ] : null;
  }
}

class TemplateTimeAction extends TemplateDatetimePickerAction {
  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null) {
    parent::__construct($label, $data, $initial, $max, $min, 'time');
  }
}

class TemplateDateAction extends TemplateDatetimePickerAction {
  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null) {
    parent::__construct($label, $data, $initial, $max, $min, 'date');
  }
}

class TemplateDatetimeAction extends TemplateDatetimePickerAction {
  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null) {
    parent::__construct($label, $data, $initial, $max, $min, 'datetime');
  }
}

class ImagemapUriAction extends ImagemapAction {
  protected $uri;

  public function __construct($uri = null, $x = null, $y = null, $width = null, $height = null) {
    $this->uri($uri)
         ->x($x)
         ->y($y)
         ->width($width)
         ->height($height);
  }

  public function uri($uri) {
    is_string($uri) && ($uri = trim($uri)) && (Func::isHttps($uri) || Func::isHttp($uri) || Func::isTel($uri)) && mb_strlen($uri) <= 1000 && $this->uri = $uri;
    return $this;
  }
  
  protected function check() {
    return isset($this->uri) && parent::check();
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::URI,
      'label' => $this->label,
      'linkUri' => $this->uri,
      'area' => $this->area(),
    ] : null;
  }
}

class ImagemapMessageAction extends ImagemapAction {
  protected $text;

  public function __construct($text = null, $x = null, $y = null, $width = null, $height = null) {
    $this->text($text)
         ->x($x)
         ->y($y)
         ->width($width)
         ->height($height);
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, 400)) && $this->text = $text;
    return $this;
  }

  protected function check() {
    return isset($this->text) && parent::check();
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::MESSAGE,
      'label' => $this->label,
      'text' => $this->text,
      'area' => $this->area(),
    ] : null;
  }
}

class QuickPostbackAction extends QuickAction {
  protected $label, $text, $data = [];

  public function __construct($label = null, array $data = [], $text = null) {
    $this->label($label)
         ->data($data)
         ->text($text);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, 300)) && $this->text = $text;
    return $this;
  }

  public function data(array $data = []) {
    $data = json_encode($data);
    $data && mb_strlen($data) <= 300 && $this->data = $data;
    return $this;
  }

  protected function check() {
    return isset($this->label) && $this->data;
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::POSTBACK,
      'label' => $this->label,
      'data' => $this->data,
      'displayText' => $this->text,
    ] : null;
  }
}

class QuickMessageAction extends QuickAction {
  protected $text;

  public function __construct($label = null, $text = null) {
    $this->label($label)
         ->text($text);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, 300)) && $this->text = $text;
    return $this;
  }
  
  protected function check() {
    return isset($this->text, $this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::MESSAGE,
      'label' => $this->label,
      'text' => $this->text,
    ] : null;
  }
}

class QuickTimeAction extends QuickDatetimePickerAction {
  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null) {
    parent::__construct($label, $data, $initial, $max, $min, 'time');
  }
}

class QuickDateAction extends QuickDatetimePickerAction {
  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null) {
    parent::__construct($label, $data, $initial, $max, $min, 'date');
  }
}

class QuickDatetimeAction extends QuickDatetimePickerAction {
  public function __construct($label = null, $data = [], $initial = null, $max = null, $min = null) {
    parent::__construct($label, $data, $initial, $max, $min, 'datetime');
  }
}

class QuickCameraAction extends QuickAction {
  protected $label;

  public function __construct($label = null) {
    $this->label($label);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  protected function check() {
    return isset($this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::CAMERA,
      'label' => $this->label,
    ] : null;
  }
}

class QuickCameraRollAction extends QuickAction {
  protected $label;

  public function __construct($label = null) {
    $this->label($label);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  protected function check() {
    return isset($this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::CAMERA_ROLL,
      'label' => $this->label,
    ] : null;
  }
}

class QuickLocationAction extends QuickAction {
  protected $label;

  public function __construct($label = null) {
    $this->label($label);
  }

  public function label($label) {
    is_string($label) && ($label = trim($label)) && ($label = Func::catStr($label, 20)) && $this->label = $label;
    return $this;
  }

  protected function check() {
    return isset($this->label);
  }

  public function buildAction() {
    return $this->check() ? [
      'type' => ActionType::LOCATION,
      'label' => $this->label,
    ] : null;
  }
}

abstract class Template {
  abstract protected function check();
  abstract public function buildTemplate();

  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'button' => '\\OA\\Line\\Message\\ButtonTemplate::create',
      'confirm' => '\\OA\\Line\\Message\\ConfirmTemplate::create',
      'carousel' => '\\OA\\Line\\Message\\CarouselTemplate::create',
      'imageCarousel' => '\\OA\\Line\\Message\\ImageCarouselTemplate::create',
    ];

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }
}

class ButtonTemplate extends Template {
  protected $title, $text, $thumbnailImageUrl, $actions, $imageAspectRatio = 'rectangle', $imageSize = 'cover', $imageBackgroundColor = '#FFFFFF', $defaultAction;

  public function __construct($title = null, $text = null, $thumbnailImageUrl = null, array $actions = [], $imageAspectRatio = null, $imageSize = null, $imageBackgroundColor = null, TemplateAction $defaultAction = null) {
    $this->title($title)
         ->text($text)
         ->thumbnailImageUrl($thumbnailImageUrl)
         ->actions($actions)
         ->imageAspectRatio($imageAspectRatio)
         ->imageSize($imageSize)
         ->imageBackgroundColor($imageBackgroundColor)
         ->defaultAction($defaultAction);
  }

  public function title($title) {
    is_string($title) && ($title = trim($title)) && ($title = Func::catStr($title, 40)) && $this->title = $title;
    $this->title && $this->text($this->text);
    return $this;
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, $this->title || $this->thumbnailImageUrl ? 60 : 160)) && $this->text = $text;
    return $this;
  }

  public function thumbnailImageUrl($thumbnailImageUrl) {
    is_string($thumbnailImageUrl) && ($thumbnailImageUrl = trim($thumbnailImageUrl)) && Func::isHttpsL1000($thumbnailImageUrl) && $this->thumbnailImageUrl = $thumbnailImageUrl;
    $this->thumbnailImageUrl && $this->text($this->text);
    return $this;
  }

  public function imageAspectRatio($imageAspectRatio) {
    is_string($imageAspectRatio) && ($imageAspectRatio = trim($imageAspectRatio)) && in_array($imageAspectRatio, ['rectangle', 'square']) && $this->imageAspectRatio = $imageAspectRatio;
    return $this;
  }

  public function imageSize($imageSize) {
    is_string($imageSize) && ($imageSize = trim($imageSize)) && in_array($imageSize, ['cover', 'contain']) && $this->imageSize = $imageSize;
    return $this;
  }

  public function imageBackgroundColor($imageBackgroundColor) {
    is_string($imageBackgroundColor) && ($imageBackgroundColor = trim($imageBackgroundColor)) && preg_match('/^#(?>[[:xdigit:]]{3}){1,2}$/', $imageBackgroundColor) && $this->imageBackgroundColor = $imageBackgroundColor;
    return $this;
  }

  public function defaultAction(TemplateAction $defaultAction = null) {
    $defaultAction && $this->defaultAction = $defaultAction->buildAction();
    return $this;
  }

  public function addAction(TemplateAction $action = null) {
    $action && array_push($this->actions, $action->buildAction());
    return $this;
  }

  public function actions(array $actions = []) {
    $this->actions = array_map(function($action) {
      return $action instanceof TemplateAction ? $action->buildAction() : null;
    }, $actions);
    return $this;
  }

  protected function check() {
    return isset($this->text) && $this->actions;
  }

  public function buildTemplate() {
    return $this->check() ? [
      'type' => TemplateType::BUTTONS,
      'thumbnailImageUrl' => $this->thumbnailImageUrl,
      'title' => $this->title,
      'text' => $this->text,
      'defaultAction' => $this->defaultAction,
      'actions' => array_slice(array_values(array_filter($this->actions)), -4),
      'imageAspectRatio' => $this->imageAspectRatio,
      'imageSize' => $this->imageSize,
      'imageBackgroundColor' => $this->imageBackgroundColor,
    ] : null;
  }
}

class ConfirmTemplate extends Template {
  protected $text, $actions;
  
  public function __construct($text = null, array $actions = []) {
    $this->text($text)
         ->actions($actions);
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, 240)) && $this->text = $text;
    return $this;
  }

  public function addAction(TemplateAction $action = null) {
    $action && array_push($this->actions, $action->buildAction());
    return $this;
  }

  public function actions(array $actions = []) {
    $this->actions = array_map(function($action) {
      return $action instanceof TemplateAction ? $action->buildAction() : null;
    }, $actions);
    return $this;
  }

  protected function check() {
    return isset($this->text) && count($this->actions) == 2;
  }

  public function buildTemplate() {
    return $this->check() ? [
      'type' => TemplateType::CONFIRM,
      'text' => $this->text,
      'actions' => array_slice(array_values(array_filter($this->actions)), -2),
    ] : null;
  }
}

class CarouselTemplate extends Template {
  protected $imageAspectRatio = 'rectangle', $imageSize = 'cover', $columns = [];

  public function __construct(array $columns = [], $imageAspectRatio = null, $imageSize = null) {
    $this->columns($columns)
         ->imageAspectRatio($imageAspectRatio)
         ->imageSize($imageSize);
  }

  public function imageAspectRatio($imageAspectRatio) {
    is_string($imageAspectRatio) && ($imageAspectRatio = trim($imageAspectRatio)) && in_array($imageAspectRatio, ['rectangle', 'square']) && $this->imageAspectRatio = $imageAspectRatio;
    return $this;
  }

  public function imageSize($imageSize) {
    is_string($imageSize) && ($imageSize = trim($imageSize)) && in_array($imageSize, ['cover', 'contain']) && $this->imageSize = $imageSize;
    return $this;
  }

  public function addColumn(CarouselTemplateColumn $column = null) {
    $column && array_push($this->columns, $column->buildColumn());
    return $this;
  }

  public function columns(array $columns) {
    $this->columns = array_map(function($column) {
      return $column instanceof CarouselTemplateColumn ? $column->buildColumn() : null;
    }, $columns);
    return $this;
  }

  protected function check() {
    // 確認每個 column 的 action 數量一致
    if (count(array_unique(array_map(function($column) { return count($column['actions']); }, $this->columns))) != 1)
      return false;

    return $this->columns;
  }

  public function buildTemplate() {
    return $this->check() ? [
      'type' => TemplateType::CAROUSEL,
      'columns' => array_slice(array_values(array_filter($this->columns)), -10),
      'imageAspectRatio' => $this->imageAspectRatio,
      'imageSize' => $this->imageSize,
    ] : null;
  }
}

class ImageCarouselTemplate extends Template {
  protected $columns = [];

  public function addColumn(ImageCarouselTemplateColumn $column = null) {
    $column && array_push($this->columns, $column->buildColumn());
    return $this;
  }

  public function columns(array $columns) {
    $this->columns = array_map(function($column) {
      return $column instanceof ImageCarouselTemplateColumn ? $column->buildColumn() : null;
    }, $columns);
    return $this;
  }

  protected function check() {
    $this->columns = array_map(function($column) { $column['action']['label'] = Func::catStr($column['action']['label'], 12); return $column; }, $this->columns);
    return $this->columns;
  }

  public function buildTemplate() {
    return $this->check() ? [
      'type' => TemplateType::IMAGE_CAROUSEL,
      'columns' => array_slice(array_values(array_filter($this->columns)), -10),
    ] : null;
  }
}

abstract class TemplateColumn {
  abstract protected function check();
  abstract public function buildColumn();

  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'carousel' => '\\OA\\Line\\Message\\CarouselTemplateColumn::create',
      'imageCarousel' => '\\OA\\Line\\Message\\ImageCarouselTemplateColumn::create',
    ];

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }
}

class CarouselTemplateColumn extends TemplateColumn {
  protected $title, $text, $thumbnailImageUrl, $actions = [], $imageBackgroundColor = '#FFFFFF', $defaultAction;

  public function __construct($title = null, $text = null, $thumbnailImageUrl = null, array $actions = [], $imageBackgroundColor = null, TemplateAction $defaultAction = null) {
    
    $this->title($title)
         ->text($text)
         ->thumbnailImageUrl($thumbnailImageUrl)
         ->actions($actions)
         ->imageBackgroundColor($imageBackgroundColor)
         ->defaultAction($defaultAction);
  }

  public function title($title) {
    is_string($title) && ($title = trim($title)) && ($title = Func::catStr($title, 40)) && $this->title = $title;
    $this->title && $this->text($this->text);
    return $this;
  }

  public function text($text) {
    is_string($text) && ($text = trim($text)) && ($text = Func::catStr($text, $this->title || $this->thumbnailImageUrl ? 60 : 120)) && $this->text = $text;
    return $this;
  }

  public function thumbnailImageUrl($thumbnailImageUrl) {
    is_string($thumbnailImageUrl) && ($thumbnailImageUrl = trim($thumbnailImageUrl)) && Func::isHttpsL1000($thumbnailImageUrl) && $this->thumbnailImageUrl = $thumbnailImageUrl;
    $this->thumbnailImageUrl && $this->text($this->text);
    return $this;
  }

  public function imageBackgroundColor($imageBackgroundColor) {
    is_string($imageBackgroundColor) && ($imageBackgroundColor = trim($imageBackgroundColor)) && preg_match('/^#(?>[[:xdigit:]]{3}){1,2}$/', $imageBackgroundColor) && $this->imageBackgroundColor = $imageBackgroundColor;
    return $this;
  }

  public function defaultAction(TemplateAction $defaultAction = null) {
    $defaultAction && $this->defaultAction = $defaultAction->buildAction();
    return $this;
  }

  public function addAction(TemplateAction $action = null) {
    $action && array_push($this->actions, $action->buildAction());
    return $this;
  }

  public function actions(array $actions = []) {
    $this->actions = array_map(function($action) {
      return $action instanceof TemplateAction ? $action->buildAction() : null;
    }, $actions);
    return $this;
  }

  protected function check() {
    return isset($this->text) && $this->actions;
  }

  public function buildColumn() {
    return $this->check() ? [
      'thumbnailImageUrl' => $this->thumbnailImageUrl,
      'title' => $this->title,
      'text' => $this->text,
      'defaultAction' => $this->defaultAction,
      'actions' => array_slice(array_values(array_filter($this->actions)), -3),
      'imageBackgroundColor' => $this->imageBackgroundColor,
    ] : null;
  }
}

class ImageCarouselTemplateColumn extends TemplateColumn {
  protected $imageUrl, $action;

  public function __construct($imageUrl = null, TemplateAction $action = null) {
    $this->imageUrl($imageUrl)
         ->action($action);
  }

  public function imageUrl($imageUrl) {
    is_string($imageUrl) && ($imageUrl = trim($imageUrl)) && Func::isHttpsL1000($imageUrl) && $this->imageUrl = $imageUrl;
    return $this;
  }

  public function action(TemplateAction $action = null) {
    $action && $this->action = $action->buildAction();
    return $this;
  }

  protected function check() {
    return isset($this->imageUrl, $this->action);
  }

  public function buildColumn() {
    return $this->check() ? [
      'imageUrl' => $this->imageUrl,
      'action' => $this->action
    ] : null;
  }
}

class ActionQuick extends Quick {
  protected $imageUrl;

  public function __construct($imageUrl = null, QuickAction $action = null) {
    $this->imageUrl($imageUrl)
         ->action($action);
  }

  public function imageUrl($imageUrl) {
    is_string($imageUrl) && ($imageUrl = trim($imageUrl)) && Func::isHttpsL1000($imageUrl) && $this->imageUrl = $imageUrl;
    return $this;
  }

  public function action(QuickAction $action = null) {
    $action && $this->action = $action->buildAction();
    return $this;
  }


  protected function check() {
    return isset($this->imageUrl, $this->action);
  }

  public function buildQuick() {
    return $this->check() ? [
      'type' => QuickType::ACTION,
      'imageUrl' => $this->imageUrl,
      'action' => $this->action
    ] : null;
  }
}

abstract class FlexTemplate {
  protected $flexAttrs = [];

  abstract protected function check();
  abstract public function buildTemplate();

  public static function create() {
    $rc = new \ReflectionClass(get_called_class());
    return $rc->newInstanceArgs(func_get_args());
  }

  public static function __callStatic($name, $arguments) {
    $funcs = [
      'bubble' => '\\OA\\Line\\Message\\BubbleFlexTemplate::create',
      'carousel' => '\\OA\\Line\\Message\\CarouselFlexTemplate::create',
    ];

    return array_key_exists($name, $funcs) ? call_user_func_array($funcs[$name], $arguments) : false;
  }
}

class BubbleFlexTemplate extends FlexTemplate {
  public function __construct(array $components) {
    $this->convert($components);
    $this->flexAttrs['type'] = FlexTemplateType::BUBBLE;
  }

  protected function check() {
    if(isset($this->flexAttrs['body'], $this->flexAttrs['type']))
      return true;
    return false;
  }

  public static function objsRecursiveToArray($objs) {
    return is_array($objs) ? array_map(function($obj) {
      return is_object($obj) ? array_map('self::objsRecursiveToArray', $obj->attrs()) : $obj;
    }, $objs) : $objs;
  }

  public function convert($components) {
    $this->flexAttrs = array_merge($this->flexAttrs, self::objsRecursiveToArray($components));
    return $this;
  }

  public function buildTemplate() {
    return !$this->check() ? null : $this->flexAttrs;
  }
}

class CarouselFlexTemplate extends FlexTemplate {
  public function __construct() {
    $this->flexAttrs['type'] = 'carousel';
  }

  public function bubbles(array $bubbles) {
    foreach($bubbles as $bubble) {
      $this->flexAttrs['contents'][] = $bubble->flexAttrs;
    }
    return $this;
  }

  protected function check() {
    if($this->flexAttrs['contents'] && is_array($this->flexAttrs['contents']))
      return true;
    return false;
  }

  public function buildTemplate() {
    return !$this->check() ? null : $this->flexAttrs;
  }
}

abstract class FlexComponents {
  protected $attrs = [];
  public function __construct() {}
  public function attrs() {
    return $this->attrs;
  }
}

class FlexStyles extends FlexComponents {
  public function __construct() {
    parent::__construct();
  }
  public static function create() {
    return new FlexStyles();
  }
  public function setHeader($value) {
    $this->attrs['header'] = $value->attrs();
    return $this;
  }
  public function setBody($value) {
    $this->attrs['body'] = $value->attrs();
    return $this;
  }
  public function setFooter($value) {
    $this->attrs['footer'] = $value->attrs();
    return $this;
  }
  public function setHero($values) {
    $this->attrs['hero'] = $value->attrs();
    return $this;
  }
}

class FlexBlock extends FlexComponents {
  public static function create() {
    return new FlexBlock;
  }
  public function setBackgroundColor($value) {
    $this->attrs['backgroundColor'] = $value;
    return $this;
  }
  public function setSeparator($value) {
    $this->attrs['separator'] = $value;
    return $this;
  }
  public function setSeparatorColor($value) {
    $this->attrs['separatorColor'] = $value;
    return $this;
  }
}

class FlexBox extends FlexComponents{
  public function __construct(array $contents) {
    parent::__construct();
    $this->attrs['type'] = 'box';
    $this->setContents($contents);
  }
  public static function create( $contents ) {
    return new FlexBox($contents);
  }
  public function setLayout($value) {
    if(is_string($value)) $this->attrs['layout'] = $value;
    return $this;
  }
  public function setContents(array $contents) {
    $this->attrs['contents'] = $contents;
    return $this;
  }
  public function setSpacing($value) {
    if(is_string($value)) $this->attrs['spacing'] = $value;
    return $this;
  }
  public function setFlex($value) {
    if(is_numeric($value)) $this->attrs['flex'] = $value;
    return $this;
  }
  public function setMargin($value) {
    if(is_string($value)) $this->attrs['margin'] = $value;
    return $this;
  }
}

class FlexButton extends FlexComponents {
  public function __construct($style) {
    $this->attrs['type'] = 'button';
    $this->setStyle($style);
  }
  public static function create($style) {
    return new FlexButton($style);
  }
  public function setAction($action) {
    $this->attrs['action'] = $action;
    return $this;
  }
  public function setFlex($value) {
    if(is_numeric($value)) $this->attrs['flex'] = $value;
    return $this;
  }
  public function setMargin($value) {
    if(is_string($value)) $this->attrs['margin'] = $value;
    return $this;
  }
  public function setHeight($value) {
    if(is_string($value)) $this->attrs['height'] = $value;
    return $this;
  }
  public function setStyle($value) {
    if(is_string($value)) $this->attrs['style'] = $value;
    return $this;
  }
  public function setColor($value) {
    if(is_string($value)) $this->attrs['color'] = $value;
    return $this;
  }
  public function setGravity($value) {
    if(is_string($value)) $this->attrs['gravity'] = $value;
    return $this;
  }
}

class FlexIcon extends FlexComponents{
  public function __construct($url) {
    parent::__construct();
    $this->attrs['type'] = 'icon';
    $this->setUrl($url);
  }
  public static function create($url) {
    return new FlexIcon($url);
  }
  public function setUrl($value) {
    if(is_string($value)) $this->attrs['url'] = $value;
    return $this;
  }
  public function setMargin($value) {
    if(is_string($value)) $this->attrs['margin'] = $value;
    return $this;
  }
  public function setSize($value) {
    if(is_string($value)) $this->attrs['size'] = $value;
    return $this;
  }
  public function setAspectRatio($value) {
    if(is_string($value)) $this->attrs['aspectRatio'] = $value;
    return $this;
  }
}

class FlexImage extends FlexComponents{
  public function __construct($url) {
    $this->attrs['type'] = 'image';
    $this->setUrl($url);
  }
  public static function create($url) {
    return new FlexImage($url);
  }
  public function setUrl($value) {
    if(is_string($value)) $this->attrs['url'] = $value;
    return $this;
  }
  public function setFlex($value) {
    if(is_numeric($value)) $this->attrs['flex'] = $value;
    return $this;
  }
  public function setMargin($value) {
    if(is_string($value)) $this->attrs['margin'] = $value;
    return $this;
  }
  public function setAlign($value) {
    if(is_string($value)) $this->attrs['align'] = $value;
    return $this;
  }
  public function setGravity($value) {
    if(is_string($value)) $this->attrs['gravity'] = $value;
    return $this;
  }
  public function setSize($value) {
    if(is_string($value)) $this->attrs['size'] = $value;
    return $this;
  }
  public function setAspectRatio($value) {
    if(is_string($value)) $this->attrs['aspectRatio'] = $value;
    return $this;
  }
  public function setAspectMode($value) {
    if(is_string($value)) $this->attrs['aspectMode'] = $value;
    return $this;
  }
  public function setBackgroundColor($value) {
    if(is_string($value)) $this->attrs['backgroundColor'] = $value;
    return $this;
  }
  public function setAction() {

  }
}

class FlexSeparator extends FlexComponents{
  public function __construct() {
    parent::__construct();
    $this->attrs['type'] = 'separator';
  }
  public static function create() {
    return new FlexSeparator();
  }
  public function setMargin($value) {
    if(is_string($value)) $this->attrs['margin'] = $value;
    return $this;
  }
  public function setColor($value) {
    if(is_string($value)) $this->attrs['color'] = $value;
    return $this;
  }
}

class FlexSpacer extends FlexComponents{
  public function __construct($size) {
    parent::__construct();
    $this->attrs['type'] = 'spacer';
    $this->setSize($size);
  }
  public static function create($size) {
    return new FlexSpacer($size);
  }
  public function setSize($value) {
    if(is_string($value)) $this->attrs['size'] = $value;
    return $this;
  }
}

class FlexText extends FlexComponents {
  public function __construct($text) {
    parent::__construct();
    $this->attrs['type'] = 'text';
    $this->setText($text);
  }
  public static function create($text) {
    return new FlexText($text);
  }
  public function setText($value) {
    if(is_string($value)) $this->attrs['text'] = $value;
    return $this;
  }
  public function setFlex($value) {
    if(is_numeric($value)) $this->attrs['flex'] = $value;
    return $this;
  }
  public function setMargin($value) {
    if(is_string($value)) $this->attrs['margin'] = $value;
    return $this;
  }
  public function setSize($value) {
    if(is_string($value)) $this->attrs['size'] = $value;
    return $this;
  }
  public function setAlign($value) {
    if(is_string($value)) $this->attrs['align'] = $value;
    return $this;
  }
  public function setGravity($value) {
    if(is_string($value)) $this->attrs['gravity'] = $value;
    return $this;
  }
  public function setWrap($value) {
    if(is_bool($value)) $this->attrs['wrap'] = $value;
    return $this;
  }
  public function setWeight($value) {
    if(is_string($value)) $this->attrs['weight'] = $value;
    return $this;
  }
  public function setColor($value) {
    if(is_string($value)) $this->attrs['color'] = $value;
    return $this;
  }
  public function setAction($value) {
    $this->attrs['action'] = $value;
    return $this;
  }
}

class FlexAction {
  public static function postBack($label, $data, $text = null) {
    return is_string($label) && is_string($text) && ($data = is_array($data) ? json_encode($data) : $data) ? [ 'type' => 'postback', 'label' => $label, 'data' => $data, 'text' => $text ] : null;
  }
  public static function message($label, $text) {
    return is_string($label) && is_string($text) ? [ 'type' => 'message', 'label' => $label, 'text' => $text ] : null;
  }
  public static function uri($label, $uri) {
    return is_string($label) && is_string($uri) ? [ 'type' => 'uri', 'label' => $label, 'uri' => $uri ] : null;
  }
  public static function datetimepicker($label, $data, $mode, $initial = null, $max = null, $min = null) {
    return is_string($label) && is_string($data) && in_array($mode, ['date', 'time', 'datetime']) ? ['type' => 'datetimepicker', 'label' => $label, 'data' => $data, 'mode' => $mode, 'initial' => $initial, 'max' => $max, 'min' => $min ] : null;
  }
}





