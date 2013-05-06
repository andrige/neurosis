<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <title><?=$title?></title>
  <link rel="stylesheet" href="<?=$stylesheet?>">
</head>
<!--
 - '$header' and such is defined in 'functions.php'.
 -->
 
<body>
  <div id="header">
    <?=$header?>
    <div id='login-menu'>
      <?=login_menu()?>
    </div>
     <div id='banner'>
        <a href='<?=base_url()?>'><img class='site-logo' src='<?=$logo?>' alt='logo' width='<?=$logo_width?>' height='<?=$logo_height?>' /></a>
        <p class='site-title'><a href='<?=base_url()?>'><?=$header?></a></p>
        <p class='site-slogan'><?=$slogan?></p>
      </div>
    </div>
   <div id='main' role='main'>
    <?=get_messages_from_session()?>
    <?=@$main?>
    <?=render_views()?>
  </div>
  <div id="footer">
    <?=get_debug()?>
    <?=$footer?>
  </div>
</body>
</html>