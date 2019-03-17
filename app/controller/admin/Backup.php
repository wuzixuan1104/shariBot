<?php defined('MAPLE') || exit('此檔案不允許讀取！');

class Backup extends AdminCrudController {
  
  public function __construct() {
    parent::__construct(\M\AdminRole::ROLE_ROOT);

    if (in_array(Router::methodName(), ['show', 'read']))
      if (!(($id = Router::params('id')) && ($this->obj = \M\Backup::one('id = ?', $id))))
        Url::refreshWithFailureFlash(Url::toRouter('AdminBackupIndex'), '找不到資料！');

    $this->view->with('title', '每日備份')
               ->with('currentUrl', Url::toRouter('AdminBackupIndex'));
  }

  public function index() {
    $list = AdminList::create('\M\Backup');

    \M\AdminAction::read('讀取每日備份列表');
    return $this->view->with('list', $list);
  }
  
  public function show() {
    $show = AdminShow::create($this->obj)
                     ->setBackUrl(Url::toRouter('AdminBackupIndex'), '回列表');
    
    \M\AdminAction::read('檢視每日備份', '檢視備份紀錄「' . \M\Backup::TYPE[$this->obj->type] . '(' . $this->obj->id . ')' . '」');
    return $this->view->with('show', $show);
  }

  public function read() {
    wtf(function() {
      return ['messages' => func_get_args()];
    });

    $params = Input::post();
    
    validator(function() use (&$params) {
      Validator::need($params, 'isRead', '已讀')->inEnum(\M\Backup::IS_READ);
    });
    
    transaction(function() use (&$params) {
      \M\AdminAction::write('設定已讀每日備份', '設定備份紀錄「' . \M\Backup::TYPE[$this->obj->type] . '(' . $this->obj->id . ')' . '」已讀狀態，將其設為「' . \M\Backup::IS_READ[$params['isRead']] . '」');
      return $this->obj->columnsUpdate($params) && $this->obj->save();
    });

    return $params;
  }
}