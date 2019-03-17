<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminCrudController extends AdminController {
  protected $obj;
  
  public function __construct() {
    parent::__construct(func_get_args());

    $this->obj = null;

    $this->view->withReference('obj', $this->obj);
  }
}
