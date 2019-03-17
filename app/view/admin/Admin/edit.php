<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use AdminFormInput as FormInput;
use AdminFormImage as FormImage;
use AdminFormCheckbox as FormCheckbox;

echo $form->back();

echo $form->form(function($obj) {

  FormImage::create('頭像', 'avatar')
    ->accept('image/*')
    ->val($obj->avatar);

  FormInput::create('名稱', 'name')
    ->need()
    ->focus()
    ->val($obj->name);

  FormInput::create('帳號', 'account')
    ->need()
    ->val($obj->account);

  FormInput::create('密碼', 'password')
    ->type('password')
    ->val('');

  FormCheckbox::create('特別權限', 'roles')
    ->items(\M\AdminRole::ROLE)
    ->val(arrayColumn($obj->roles, 'role'));
});