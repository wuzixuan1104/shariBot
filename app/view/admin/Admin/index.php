<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use AdminListSearchInput as SearchInput;
use AdminListSearchCheckbox as SearchCheckbox;

use AdminListText as ListText;
use AdminListCtrl as ListCtrl;
use AdminListImage as ListImage;
use AdminListItems as ListItems;

echo $list->search(function() {

  SearchInput::create('ID')
    ->sql('id = ?');

  SearchInput::create('名稱')
    ->sql('name LIKE ?');

  SearchCheckbox::create('角色')
    ->sql(function($val) { return Where::create('id IN (?)', \M\AdminRole::arr('adminId', 'role IN (?)', $val)); })
    ->items(\M\AdminRole::ROLE);
});

echo $list->table(function($obj) {
  
  ListText::create('ID')
    ->content($obj->id)
    ->width(60)
    ->order('id');

  ListImage::create('頭像')
    ->content($obj->avatar);

  ListText::create('名稱')
    ->content($obj->name)
    ->order('name');

  ListItems::create('標籤')
    ->content(array_map(function($role) { return \M\AdminRole::ROLE[$role->role]; }, $obj->roles))
    ->width(200);

  ListText::create('操作次數')
    ->content(number_format(count($obj->actions)) . '次')
    ->width(100);

  ListText::create('新增時間')
    ->content($obj->createAt)
    ->width(150);

  ListCtrl::create()
    ->addShow('AdminAdminShow', $obj)
    ->addEdit('AdminAdminEdit', $obj)
    ->addDelete('AdminAdminDelete', $obj);
});

echo $list->pages();
