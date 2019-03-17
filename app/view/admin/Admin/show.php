<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use AdminShowText as ShowText;
use AdminShowItems as ShowItems;
use AdminShowImage as ShowImage;

echo $show->back();

echo $show->panel(function($obj, &$title) {
  ShowText::create('ID')
    ->content($obj->id);

  ShowImage::create('頭像')
    ->content($obj->avatar);

  ShowText::create('名稱')
    ->content($obj->name);

  ShowItems::create('角色')
    ->content(array_map(function($role) { return \M\AdminRole::ROLE[$role->role]; }, $obj->roles));

  ShowText::create('新增時間')
    ->content($obj->createAt);
});
