<?php defined('MAPLE') || exit('此檔案不允許讀取！');

Router::dir('admin', 'Admin', function() {

  // 登入
  Router::get('logout')->controller('Auth@logout');
  Router::get('login')->controller('Auth@login');
  Router::post('login')->controller('Auth@signin');

  // 後台主頁
  Router::get()->controller('Main@index');
  Router::post('theme')->controller('Main@theme');

  // Admin
  Router::get('admins')->controller('Admin@index');
  Router::get('admins/add')->controller('Admin@add');
  Router::post('admins')->controller('Admin@create');
  Router::get('admins/(id:num)/edit')->controller('Admin@edit');
  Router::put('admins/(id:num)')->controller('Admin@update');
  Router::get('admins/(id:num)')->controller('Admin@show');
  Router::del('admins/(id:num)')->controller('Admin@delete');
  
  // Backup
  Router::get('backups')->controller('Backup@index');
  Router::get('backups/(id:num)')->controller('Backup@show');
  Router::post('backups/(id:num)/read')->controller('Backup@read');

  // Crontab
  Router::get('crontabs')->controller('Crontab@index');
  Router::get('crontabs/(id:num)')->controller('Crontab@show');
  Router::post('crontabs/(id:num)/read')->controller('Crontab@read');

});