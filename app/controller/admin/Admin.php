<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Admin extends AdminCrudController {
  private $ignoreIds;
  
  public function __construct() {
    parent::__construct(\M\AdminRole::ROLE_ADMIN, \M\AdminRole::ROLE_ROOT);

    wtfTo('AdminAdminIndex');

    $this->ignoreIds = [1];

    if (in_array(Router::methodName(), ['edit', 'update', 'delete', 'show']))
      if (!$this->obj = \M\Admin::one('id = ? AND id NOT IN(?)', Router::params('id'), $this->ignoreIds))
        error('找不到資料！');

    $this->view->with('title', '管理員帳號')
               ->with('currentUrl', Url::toRouter('AdminAdminIndex'));
  }

  public function index() {
    $where = Where::create('id NOT IN(?)', $this->ignoreIds);

    $list = AdminList::create('\M\Admin', ['include' => ['roles', 'actions'], 'where' => $where])
                     ->setAddUrl(Url::toRouter('AdminAdminAdd'));

    \M\AdminAction::read('讀取後台管理員列表');
    return $this->view->with('list', $list);
  }
  
  public function add() {
    $form = AdminForm::create()
                     ->setActionUrl(Url::toRouter('AdminAdminCreate'))
                     ->setBackUrl(Url::toRouter('AdminAdminIndex'));

    \M\AdminAction::read('準備新增後台管理員');
    return $this->view->with('form', $form);
  }
  
  public function create() {
    wtfTo('AdminAdminAdd');

    $params = Input::post();
    $files  = Input::file();

    validator(function() use (&$params, &$files) {
      Validator::need($params, 'name', '名稱')->isVarchar(190);
      Validator::need($params, 'account', '帳號')->isVarchar(190);
      Validator::need($params, 'password', '密碼')->isPassword();
      Validator::maybe($params, 'roles', '角色', [])->filter(array_keys(\M\AdminRole::ROLE));
      Validator::maybe($files,  'avatar', '頭像')->isUploadFile(config('upload', 'picture'));
      $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
    });

    transaction(function() use (&$params, &$files) {
      if (!$obj = \M\Admin::create($params))
        return false;

      if(!$obj->putFiles($files))
        return false;

      foreach ($params['roles'] as $role)
        if (!\M\AdminRole::create(['adminId' => $obj->id, 'role' => $role]))
          return false;

      \M\AdminAction::write('新增後台管理員', '新增後台管理員，其 ID 為「' . $obj->id . '」、名稱為「' . $obj->name . '」' . ($params['roles'] ? '，角色權限有：' . implode(', ', $params['roles']) : ''));
      return true;
    });


    Url::refreshWithSuccessFlash(Url::toRouter('AdminAdminIndex'), '新增成功！');
  }
  
  public function edit() {
    $form = AdminForm::create($this->obj)
                     ->setActionUrl(Url::toRouter('AdminAdminUpdate', $this->obj))
                     ->setBackUrl(Url::toRouter('AdminAdminIndex'));
    
    \M\AdminAction::read('準備修改後台管理員', '準備修改後台管理員「' . $this->obj->name . '(' . $this->obj->id . ')' . '」');
    return $this->view->with('form', $form);
  }
  
  public function update() {
    wtfTo('AdminAdminEdit', $this->obj);

    $params = Input::post();
    $files  = Input::file();
    
    validator(function() use (&$params, &$files) {
      Validator::need($params, 'name', '名稱')->isVarchar(190);
      Validator::need($params, 'account', '帳號')->isVarchar(190);
      Validator::maybe($params, 'password', '密碼', '')->isPassword();
      Validator::maybe($params, 'roles', '角色', [])->filter(array_keys(\M\AdminRole::ROLE));
      Validator::maybe($files,  'avatar', '頭像')->isUploadFile(config('upload', 'picture'));
      
      if ($params['password'])
        $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
      else
        unset($params['password']);
    });

    transaction(function() use (&$params, &$files) {
      if (!($this->obj->columnsUpdate($params) && $this->obj->save()))
        return false;

      if(!$this->obj->putFiles($files))
        return false;

      $oris = arrayColumn($this->obj->roles, 'role');
      $dels = array_diff($oris, $params['roles']);
      $adds = array_diff($params['roles'], $oris);

      foreach ($dels as $del)
        if ($role = \M\AdminRole::one('adminId = ? AND role = ?', $this->obj->id, $del))
          if (!$role->delete())
            return false;

      foreach ($adds as $add)
        if (!\M\AdminRole::create(['adminId' => $this->obj->id, 'role' => $add]))
          return false;
      
      \M\AdminAction::write('修改後台管理員', '修改後台管理員「' . $this->obj->name . '(' . $this->obj->id . ')' . '」' . ($params['roles'] ? '，目前角色權限有：' . implode(', ', $params['roles']) : ''));
      return true;
    });
    
    Url::refreshWithSuccessFlash(Url::toRouter('AdminAdminIndex'), '修改成功！');
  }
  
  public function show() {
    $where = Where::create('adminId = ?', $this->obj->id);
    $list = AdminList::create('\M\AdminAction', ['order' => 'id DESC', 'where' => $where]);

    $show = AdminShow::create($this->obj)
                     ->setBackUrl(Url::toRouter('AdminAdminIndex'), '回列表');
    
    \M\AdminAction::read('檢視後台管理員', '檢視後台管理員「' . $this->obj->name . '(' . $this->obj->id . ')' . '」的詳細資料');
    return $this->view->with('show', $show)
                      ->with('list', $list);
  }
  
  public function delete() {
    wtfTo('AdminAdminIndex');
    
    transaction(function() {
      \M\AdminAction::write('刪除後台管理員', '刪除後台管理員「' . $this->obj->name . '(' . $this->obj->id . ')' . '」');
      return $this->obj->delete();
    });

    Url::refreshWithSuccessFlash(Url::toRouter('AdminAdminIndex'), '刪除成功！');
  }
}
