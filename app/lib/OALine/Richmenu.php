<?php
namespace OA\Line\Richmenu;
use OA\Line\Bot;

defined('MAPLE') || exit('此檔案不允許讀取！');

class Richmenu {
  private $sizeBuilder, $selected, $name, $chatBarText;
  private $areaBuilders = [];

  public function __construct($sizeBuilder, $selected, $name, $chatBarText, $areaBuilders) {
    $this->sizeBuilder = $sizeBuilder;
    $this->selected = $selected;
    $this->name = $name;
    $this->chatBarText = $chatBarText;
    $this->areaBuilders = $areaBuilders;
  }

  public static function create($sizeBuilder, $selected, $name, $chartBarText, $areaBuilders) {
    return new self($sizeBuilder, $selected, $name, $chartBarText, $areaBuilders);
  }

  public function build() {
    $areas = [];
    foreach ($this->areaBuilders as $areaBuilder) {
        $areas[] = $areaBuilder->build();
    }

    return [
        'size' => $this->sizeBuilder->build(),
        'selected' => $this->selected,
        'name' => $this->name,
        'chatBarText' => $this->chatBarText,
        'areas' => $areas,
    ];
  }
}

class RichmenuSize {
  private $height, $width;

  public function __construct($height, $width) {
    $this->height = $height;
    $this->width = $width;
  }

  public static function getFull() {
    return new self(1686, 2500);
  }

  public static function getHalf() {
    return new self(843, 2500);
  }

  public function build() {
    return [
      'height' => $this->height,
      'width' => $this->width,
    ];
  }
}

class RichmenuArea {
  private $boundsBuilder, $actionBuilder;
  public function __construct($boundsBuilder, $actionBuilder) {
    $this->boundsBuilder = $boundsBuilder;
    $this->actionBuilder = $actionBuilder;
  }

  public static function create($boundsBuilder, $actionBuilder) {
    return new self($boundsBuilder, $actionBuilder);
  }

  public function build() {
    return [
        'bounds' => $this->boundsBuilder->build(),
        'action' => $this->actionBuilder,
    ];
  }
}

class RichmenuAreaBounds {
  private $x, $y, $width, $height;
  public function __construct($x, $y, $width, $height) {
    $this->x = $x;
    $this->y = $y;
    $this->width = $width;
    $this->height = $height;
  }

  public static function create($x, $y, $width, $height) {
    return new self($x, $y, $width, $height);
  }

  public function build() {
    return [
      'x' => $this->x,
      'y' => $this->y,
      'width' => $this->width,
      'height' => $this->height
    ];
  }
}

class RichmenuAction {
  public static function postBack($label, $data, $text = null) {
    $data = is_array($data) ? json_encode($data) : $data;
    return (is_string($label) && is_string($text) && $data) ? [ 'type' => 'postback', 'label' => $label, 'data' => $data, 'displayText' => $text ] : null;
  }
  public static function uri($label, $uri) {
    return is_string($label) && is_string($uri) ? [ 'type' => 'uri', 'label' => $label, 'uri' => $uri ] : null;
  }
}

class Generator {
  public static function create4user($sid, $richmenuId) {
    if(!Bot::instance()->linkRichMenu($sid, $richmenuId))
      return false;
    return true;
  }
  
  public static function create($filePath = '/Users/wuzixuan/www/line@/shariBot/asset/img/richmenu/v1.png') {
    if(!$richMenu = Bot::instance()->createRichMenu(Richmenu::create(
      RichmenuSize::getHalf(), true, '旅遊咖 : ' . date('Y-m-d H:i:s'), '來去旅遊摟 GO!',
      [ 
        RichmenuArea::create(RichmenuAreaBounds::create(0, 0, 833, 843), RichmenuAction::postback('精選團旅', ['a', 'b'], '已點擊 精選團旅')),
        RichmenuArea::create(RichmenuAreaBounds::create(625, 0, 833, 843), RichmenuAction::postback('優惠團旅', ['a', 'b'], '已點擊 優惠團旅')),
        RichmenuArea::create(RichmenuAreaBounds::create(1250, 0, 833, 843), RichmenuAction::postback('會員中心', ['a', 'b'], '已點擊 會員中心')),
        RichmenuArea::create(RichmenuAreaBounds::create(1874, 0, 833, 843), RichmenuAction::postback('問題專區', ['a', 'b'], '已點擊 問題專區')),
      ]
    )))
      return false;

    if (!$richMenu->isSucceeded)
      return false;

    $richMenuId = $richMenu->jsonBody['richMenuId'];
  
    if (!$img = Bot::instance()->uploadRichMenuImage($richMenuId, $filePath, 'image/png'))
      return false;

    if (!$riches = Bot::instance()->getRichMenuList()) 
      return false;

    foreach ($riches->jsonBody['richmenus'] as $rich) {
      if ($rich['richMenuId'] == $richMenuId) {
        if (!\M\LineRichmenu::createNewOne($rich)) {
          return false;
        }
      }
    }
    return true;
  }
}