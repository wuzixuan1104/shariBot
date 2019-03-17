<!DOCTYPE html>
<html lang="tw">
  <head>
    <meta http-equiv="Content-Language" content="zh-tw" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui" />

    <title><?php echo isset($title) && $title ? (is_array($title) ? $title[0] : $title) . ' | ' : '';?>後台系統</title>

    <?php echo $asset->renderCSS();?>
    <?php echo $asset->renderJS();?>

  </head>
  <body lang="zh-tw">

    <main id='main'>
      <header id='main-header'>
        <a id='hamburger' class='icon-01'></a>
        <nav>
    <?php echo isset($title) && $title ? is_array($title) ? implode('', array_map(function($text) { return '<b>' . $text . '</b>'; }, $title)) : '<b>' . $title . '</b>' : '';?>
          <label>
            <select id='theme' data-url='<?php echo Url::toRouter('AdminMainTheme');?>'>
              <option value='blue'<?php echo $theme === 'blue' ? ' selected' : '';?>>藍色 主題</option>
              <option value='green'<?php echo $theme === 'green' ? ' selected' : '';?>>綠色 主題</option>
            </select>
          </label>
        </nav>
        <a href='<?php echo Url::toRouter('AdminAuthLogout');?>' class='icon-02'></a>
      </header>

      <div class='flash <?php echo $flash['type'];?>'><?php echo $flash['msg'];?></div>

      <div id='container'><?php echo isset($content) ? $content : ''; ?></div>

    </main>

    <div id='menu'>
      <header id='menu-header'>
        <a href='<?php echo Url::base();?>' class='icon-21'></a>
        <span>後台系統</span>
      </header>

      <div id='menu-user'>
        <figure class='_ic'>
          <img src="<?php echo \M\Admin::current()->avatar->url();?>">
        </figure>

        <div>
          <span>Hi, 您好!</span>
          <b><?php echo \M\Admin::current()->name;?></b>
        </div>
      </div>

      <div id='menu-main'>
  <?php echo AdminLayout::menu([
          '後台設定 | icon-14' => [
            '後台首頁' => 'AdminMainIndex | icon-21',
            '管理員帳號' => 'AdminAdminIndex | icon-15',
            '每日備份' => 'AdminBackupIndex | icon-46 | data-cntlabel=backup-isRead | data-cnt=' . \M\Backup::count('isRead = ?', \M\Backup::IS_READ_NO),
            '排程紀錄' => 'AdminCrontabIndex | icon-62 | data-cntlabel=crontab-isRead | data-cnt=' . \M\Crontab::count('isRead = ?', \M\Crontab::IS_READ_NO),
          ]
        ], $currentUrl); ?>
      </div>
    </div>

    <footer id='footer'><span>後台版型設計 by </span><a href='https://www.ioa.tw/' target='_blank'>OAWU</a></footer>
    <div id='submit-loading'><div><span>表單送出中，請稍候</span></div></div>
    

  </body>
</html>
