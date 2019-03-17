<?php

namespace M;

defined('MAPLE') || exit('此檔案不允許讀取！');

class LineImage extends Model {
  // static $hasOne = [];

  // static $hasMany = [];

  // static $belongToOne = [];

  // static $belongToMany = [];

  static $uploaders = [
    'file' => 'LogImageFileFileUploader',
  ];

  public function putFiles($files) {
    foreach ($files as $key => $file)
      if ($file && isset($this->$key) && $this->$key instanceof Uploader && !$this->$key->put($file))
        return false;
    return true;
  }
}

class LogImageFileFileUploader extends FileUploader {}
