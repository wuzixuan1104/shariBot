<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class CrontabController extends CliController {
  protected $crontab = null;
  
  public function __construct() {
    parent::__construct();

    wtf(function($error) {
      // 發推波
      echo '<meta http-equiv="Content-type" content="text/html; charset=utf-8" /><pre>';
      var_dump($error);
      exit();
    });

    $this->crontab = \M\Crontab::create([
      'title' => '',
      'method' => Router::methodName(),
      'params' => json_encode(Router::params()),
      'status' => \M\Crontab::STATUS_FAILURE,
      'isRead' => \M\Crontab::IS_READ_NO,
      'sTime' => microtime(true),
      'eTime' => 0,
      'rTime' => 0,
    ]);

    $this->crontab || error('新增 Crontab 失敗！');
  }

  public function __destruct() {
    $this->crontab->status = \M\Crontab::STATUS_SUCCESS;
    $this->crontab->isRead = \M\Crontab::IS_READ_YES;
    $this->crontab->eTime  = microtime(true);
    $this->crontab->rTime  = $this->crontab->eTime - $this->crontab->sTime;
    $this->crontab->save();
  }
}
