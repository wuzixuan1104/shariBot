<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Load::sysLib('Html.php');

spl_autoload_register(function($className) {
  if (!preg_match("/^Admin(.*)(?<!Controller)$/", $className))
    return false;

  $file = implode(DIRECTORY_SEPARATOR, (array_filter(preg_split('/(?=[A-Z])/', $className)))) . '.php';

  Load::sysLib($file);

  class_exists($className) || gg('找不到名稱為「' . $className . '」的 Model 物件！');
}, false, true);

class AdminLayout {
  private static function toString($menu, $currentUrl) {
    return '<div>' . implode('', array_map(function($group) use ($currentUrl) {
      $text  = $group['text'];
      $icon  = $group['icon'];
      $links = $group['links'];

      unset($group['text']);
      unset($group['icon']);
      unset($group['links']);

      return Span::create($text)->className($icon)->attrs($group) . '<div>' . implode('', array_map(function($link) use ($currentUrl) {
        $name = $link['name'];
        $text = $link['text'];
        $icon = $link['icon'];

        unset($link['name']);
        unset($link['text']);
        unset($link['icon']);

        $url = call_user_func_array('Url::toRouter', $name);
        return Hyperlink::create($url)->text($text)->className($icon . ($currentUrl == $url ? ' active' : ''))->attrs($link);
      }, $links)) . '</div>';
    }, $menu)) . '</div>';
  }

  public static function menu($menu, $currentUrl) {
    $newMenu = [];
    Load::sysFunc('file.php');
    Load::sysLib('Html.php');
    
    foreach ($menu as $title => $links) {
      $tmps = [];
      $dataCntlabels = [];
      $dataCnts = [];

      foreach ($links as $text => $link) {
        if ($link === null)
          continue;

        if (is_string($link)) {
          $attrs = array_filter(array_map('trim', preg_split('/\s*\|\s*/', $link)));
          $name = array_shift($attrs);
          $icon = array_shift($attrs);

          $link = ['name' => $name, 'icon' => $icon];
          foreach ($attrs as $attr) {
            list($key, $val) = array_pad(explode('=', $attr, 2), 2, 'a');
            $link[$key] = $val;
          }
        }

        is_string($link['name']) && $link['name'] = [$link['name']];

        if (!$router = Router::findByName($link['name'][0]))
          continue;
      
        $content = fileRead(PATH_CONTROLLER . $router->path() . $router->className() . '.php');

        if ($content && preg_match_all('/parent::__construct\s*\((?P<params>.*)\)/', $content, $r) && $r['params']) {
          $content = '[' . $r['params'][0] . ']';
          eval('$content=' . $content . ';');

          if (is_array($content) && ($content = arrayFlatten($content)) && !\M\Admin::current()->inRoles($content))
            continue;
        }

        isset($link['data-cntlabel']) && array_push($dataCntlabels, $link['data-cntlabel']);
        isset($link['data-cnt']) && array_push($dataCnts, $link['data-cnt']);

        array_push($tmps, array_merge([
          'text' => $text,
          'name' => 'AdminMainIndex',
          'icon' => null,
          'data-cntlabel' => null,
          'data-cnt' => null,
        ], $link));
      }
        
      if (!$tmps)
        continue;

      $dataCntlabels = $dataCntlabels ? implode(' ', $dataCntlabels) : null;
      $dataCnts = array_sum($dataCnts);
      $dataCnts = $dataCnts ? $dataCnts : null;

      $attrs = array_filter(array_map('trim', preg_split('/\s*\|\s*/', $title)));
      $text = array_shift($attrs);
      $icon = array_shift($attrs);

      $link = ['text' => $text, 'icon' => $icon, 'links' => $tmps, 'data-cntlabel' => $dataCntlabels, 'data-cnt' => $dataCnts];
      foreach ($attrs as $attr) {
        list($key, $val) = array_pad(explode('=', $attr, 2), 2, 'a');
        $link[$key] = $val;
      }

      array_push($newMenu, $link);
    }

    return self::toString($newMenu, $currentUrl);
  }
}