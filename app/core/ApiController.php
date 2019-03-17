<?php defined('MAPLE') || exit('此檔案不允許讀取！');

abstract class ApiController extends Controller {
  
  public function __construct() {
    parent::__construct();

    wtf(function() {
      return ['messages' => func_get_args()];
    });

    GG::$isApi = true;

    Load::sysFunc('format.php');
    Load::sysLib('Validator.php');
  }
}
