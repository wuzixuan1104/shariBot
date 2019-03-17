<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Auth extends Controller {
  
  public function __construct() {
    Load::sysLib('Asset.php');
    Load::sysLib('Session.php');
    Load::sysLib('Validator.php');
  }

  public function logout() {
    Session::unsetData('admin');
    return Url::refreshWithSuccessFlash(Url::toRouter('AdminAuthLogin'), '登出成功！');
  }

  public function login() {
    $flash = Session::getFlashData('flash');

    $asset = Asset::create()
                  ->addCSS('/asset/css/icon-admin-login.css')
                  ->addCSS('/asset/css/admin/login.css')
                  ->addJS('/asset/js/res/jquery-1.10.2.min.js')
                  ->addJS('/asset/js/login.js');

    return View::create('admin/Auth/login.php')
               ->with('asset', $asset)
               ->with('flash', $flash);
  }

  public function signin() {
    wtfTo('AdminAuthLogin');

    $params = Input::post();
    
    validator(function() use (&$params, &$admin) {
      Validator::need($params, 'account', '帳號')->isVarchar(190);
      Validator::need($params, 'password', '密碼')->isPassword();
      
      $admin = \M\Admin::one('account = ?', $params['account']);
      $admin || error('此帳號不存在！');

      password_verify($params['password'], $admin->password) || error('密碼錯誤！');
    });

    transaction(function() use (&$admin) {
      return $admin->save();
    });

    Session::setData('admin', $admin);
    Url::refreshWithSuccessFlash(Url::toRouter('AdminMainIndex'), '登入成功！');
  }
}
