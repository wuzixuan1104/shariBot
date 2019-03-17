<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use AdminFormInput as FormInput;
use AdminFormImage as FormImage;
use AdminFormCheckbox as FormCheckbox;

echo $form->back();

echo $form->form(function() {

  FormImage::create('頭像', 'avatar')
    ->accept('image/*');

  FormInput::create('名稱', 'name')
    ->need()
    ->focus();

  FormInput::create('帳號', 'account')
    ->need();

  FormInput::create('密碼', 'password')
    ->type('password')
    ->need();

  FormCheckbox::create('特別權限', 'roles')
    ->items(\M\AdminRole::ROLE);
});