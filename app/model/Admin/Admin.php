<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class Admin extends Model {
  // static $hasOne = [];

  static $hasMany = [
    'roles' => 'AdminRole',
    'actions' => 'AdminAction',
  ];

  // static $belongToOne = [];

  // static $belongToMany = [];

  static $uploaders = [
    'avatar' => 'AdminAvatarImageUploader',
  ];

  public function inRoles() {
    foreach (arrayFlatten(func_get_args()) as $arg)
      if (in_array($arg, arrayColumn($this->roles, 'role')))
        return true;

    return false;
  }

  public static function current() {
    static $current;
    return !$current && \Session::getData('admin') ? $current = Admin::one('id = ?', \Session::getData('admin')->id) : $current;
  }
  
  public function putFiles($files) {
    foreach ($files as $key => $file)
      if ($file && isset($this->$key) && $this->$key instanceof Uploader && !$this->$key->put($file))
        return false;
    return true;
  }

  public function delete() {
    foreach ($this->roles as $role)
      if (!$role->delete())
        return false;

    return parent::delete();
  }
}

class AdminAvatarImageUploader extends ImageUploader {
  
  protected function d4Url() {
    \Load::sysLib('Asset.php');
    return \Asset::url('asset/img/admin.png');
  }

  public function versions() {
    return [
      'w100' => ['resize' => [100, 100, 'width']],
      'c120x120' => ['adaptiveResizeQuadrant' => [120, 120, 'c']],
    ];
  }
}
