<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class AdminController extends Controller {
  protected $asset, $view;
  public $flash, $roles = [];
  
  public function __construct() {
    parent::__construct();
    
    wtfTo('AdminMainIndex');
    $this->roles = arrayFlatten(func_get_args());

    Load::sysLib('Asset.php');
    Load::sysLib('Session.php');
    Load::sysLib('Validator.php');
    Load::sysLib('Html.php');
    Load::sysLib('Admin' . DIRECTORY_SEPARATOR . 'Admin.php');

    if (!\M\Admin::current())
      return Url::refreshWithFailureFlash(Url::base('admin', 'login'), '請先登入！');

    if ($this->roles && !\M\Admin::current()->inRoles($this->roles))
      return error('您的權限不符！');

    $this->asset = Asset::create(1)
         ->addCSS('/asset/css/res/jquery.datetimepicker.css')
         ->addCSS('/asset/css/icon-admin.css')
         ->addCSS('/asset/css/admin/layout.css')
         ->addCSS('/asset/css/admin/list.css')
         ->addCSS('/asset/css/admin/form.css')
         ->addCSS('/asset/css/admin/show.css')

         ->addJS('/asset/js/res/jquery-1.10.2.min.js')
         ->addJS('/asset/js/res/jquery_ui_v1.12.1.js')
         ->addJS('/asset/js/res/jquery_ujs.js')
         ->addJS('/asset/js/res/imgLiquid-min.js')
         ->addJS('/asset/js/res/timeago.js')
         ->addJS('/asset/js/res/datetimepicker/jquery.datetimepicker.full.js')
         ->addJS('/asset/js/res/oaips-20180115.js')
         ->addJS('/asset/js/res/autosize-3.0.8.js')
         ->addJS('/asset/js/res/OAdropUploadImg-20180115.js')
         ->addJS('/asset/js/res/ckeditor_d2015_05_18/ckeditor.js')
         ->addJS('/asset/js/res/ckeditor_d2015_05_18/adapters/jquery.js')
         ->addJS('/asset/js/res/ckeditor_d2015_05_18/plugins/tabletools/tableresize.js')
         ->addJS('/asset/js/res/ckeditor_d2015_05_18/plugins/dropler/dropler.js')
         ->addJS('/asset/js/admin/layout.js');

    $this->flash = Session::getFlashData('flash');
    !isset($this->flash['params']) || $this->flash['params'] || $this->flash['params'] = null;

    if (($theme = Session::getData('theme')) == 'green')
      $this->asset->addCSS ('/asset/css/admin/cherry.css');

    $this->view = View::maybe('admin/' . Router::className() . '/' . Router::methodName() . '.php')
                      ->appendTo(View::create('admin/layout.php'), 'content')
                      ->with('flash', $this->flash)
                      ->with('currentUrl', null)
                      ->with('theme', $theme)
                      ->withReference('asset', $this->asset);
  }
}
