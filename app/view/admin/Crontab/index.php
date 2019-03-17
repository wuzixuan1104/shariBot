<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use AdminListSearchInput as SearchInput;
use AdminListSearchCheckbox as SearchCheckbox;

use AdminListText as ListText;
use AdminListSwitcher as ListSwitcher;

echo $list->search(function() {

  SearchInput::create('ID')
    ->sql('id = ?');

  SearchInput::create('標題')
    ->sql('title LIKE ?');

  SearchInput::create('Method')
    ->sql('method LIKE ?');

  SearchCheckbox::create('狀態')
    ->sql('status IN (?)')
    ->items(\M\Crontab::STATUS);

  SearchCheckbox::create('已讀')
    ->sql('isRead IN (?)')
    ->items(\M\Crontab::IS_READ);
});

echo $list->table(function($obj) {
  
  ListSwitcher::create('已讀')
    ->on(\M\Crontab::IS_READ_YES)
    ->off(\M\Crontab::IS_READ_NO)
    ->url(Url::toRouter('AdminCrontabRead', $obj))
    ->column('isRead')
    ->cntLabel('crontab-isRead');

  ListText::create('ID')
    ->content($obj->id)
    ->width(60)
    ->order('id');

  ListText::create('Method')
    ->content($obj->method)
    ->width(140);

  ListText::create('標題')
    ->content($obj->title);

  ListText::create('參數')
    ->content(minText($obj->params))
    ->width(120);

  ListText::create('耗時')
    ->content(number_format($obj->rTime, 4) . ' 秒')
    ->width(120)
    ->order('rTime');

  ListText::create('狀態')
    ->content(Span::create(\M\Crontab::STATUS[$obj->status])->className($obj->status == \M\Crontab::STATUS_SUCCESS ? 'green' : 'red'))
    ->width(80)
    ->order('status');

  ListText::create('新增時間')
    ->content($obj->createAt)
    ->width(150);
});

echo $list->pages();
