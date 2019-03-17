<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use AdminListSearchInput as SearchInput;
use AdminListSearchCheckbox as SearchCheckbox;

use AdminListText as ListText;
use AdminListSwitcher as ListSwitcher;

echo $list->search(function() {

  SearchInput::create('ID')
    ->sql('id = ?');

  SearchInput::create('日期')
    ->sql('DATE(createAt) = ?')
    ->type('date');

  SearchInput::create('大於(Byte)')
    ->sql('size >= ?')
    ->type('number');

  SearchCheckbox::create('類型')
    ->sql('type IN (?)')
    ->items(\M\Backup::TYPE);

  SearchCheckbox::create('狀態')
    ->sql('status IN (?)')
    ->items(\M\Backup::STATUS);

  SearchCheckbox::create('已讀')
    ->sql('isRead IN (?)')
    ->items(\M\Backup::IS_READ);
});

echo $list->table(function($obj) {
  
  ListSwitcher::create('已讀')
    ->on(\M\Backup::IS_READ_YES)
    ->off(\M\Backup::IS_READ_NO)
    ->url(Url::toRouter('AdminBackupRead', $obj))
    ->column('isRead')
    ->cntLabel('backup-isRead');

  ListText::create('ID')
    ->content($obj->id)
    ->width(60)
    ->order('id');

  ListText::create('類型')
    ->content(\M\Backup::TYPE[$obj->type]);

  ListText::create('下載')
    ->content(Hyperlink::create($obj->file)->text('下載')->attrs(['download' => (string)$obj->file]))
    ->width(80);

  ListText::create('大小')
    ->content(number_format($obj->size) . ' Byte')
    ->width(120)
    ->order('size');

  ListText::create('狀態')
    ->content(Span::create(\M\Backup::STATUS[$obj->status])->className($obj->status == \M\Backup::STATUS_SUCCESS ? 'green' : 'red'))
    ->width(80)
    ->order('status');

  ListText::create('新增時間')
    ->content($obj->createAt)
    ->width(150);
});

echo $list->pages();
