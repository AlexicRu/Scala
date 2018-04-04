<!DOCTYPE html>
<html lang="ru-RU">
<head>

    <?=Common::getFaviconRawData($customView)?>

	<meta charset="UTF-8">

	<title><?=$title?></title>

	<?foreach($styles as $style){?>
		<link href="<?=$style?>?t=<?=Common::getVersion()?>" rel="stylesheet">
	<?}?>
	<?foreach($scripts as $script){?>
		<script src="<?=$script?>?t=<?=Common::getVersion()?>"></script>
	<?}?>

	<!--[if lt IE 9]>
	<style>
		.bad_browser{display:block;}
		.wrapper, header{display:none;}
	</style>
	<![endif]-->
</head>

<body class="custom_view_<?=$customView?>">
	<header>
		<div class="logo">
			<a href="/"></a>
		</div>
		<div class="hamburger">
            <?if (empty($user['managers_binds'])) {?>
			    <a href="#" class="menu-toggle"><span class="icon-menu"></span></a>
            <?}else{
                $currentManager = reset($user['managers_binds']);
                ?>
                <a href="#" class="clients-toggle"><span class="icon-clients"></span> <?=$currentManager['WEB_NAME_CURRENT']?></a>
                <div class="clients-float-list clients-toggle block customScroll">
                    <?foreach ($user['managers_binds'] as $manager) {?>
                        <div>
                            <?=$manager['WEB_NAME_TO']?> &nbsp; <a href="/force-login/<?=Common::encrypt($user['MANAGER_ID'] . ' ' . $manager['MANAGER_TO'])?>" class="btn btn_small">Переключиться</a>
                        </div>
                    <?}?>
                </div>
            <?}?>
		</div>
		<div class="search">
			<form action="/clients" method="post"><button class="icon-find"></button><input type="text" name="search" placeholder="Поиск..." value="<?=(!empty($_REQUEST['search']) ? Text::quotesForForms($_REQUEST['search']) : '')?>"></form>
		</div>
		<div class="personal">
			<div class="avatar" <?/*style="background-image: url(/img/pic/01.png)"*/?>><i class="icon-user"></i></div>
			<div class="personal_name"><?=User::getName($user)?></div>
		</div>
		<div class="mail">
			<a href="/messages"><span class="icon-mail"><?if(count($notices)){?><span><?=count($notices)?></span><?}?></span></a>
			<?if(count($notices)){?>
				<div class="notices customScroll">
					<?
					$i = 5;
					foreach($notices as $notice){
						if($i-- < 0){
							break;
						}
						?>
						<div class="notice">
							<div class="n_title"><?=$notice['SUBJECT']?></div>
							<?=$notice['NOTIFICATION_BODY']?>
						</div>
					<?}?>
					<div>
						<a href="#" class="mark_read"><i class="icon-ok"></i> Отметить прочитанным</a>
					</div>
				</div>
			<?}?>
		</div>
		<div class="settings">
			<a href="/managers/settings"><span class="icon-gear"></span></a>
		</div>
		<div class="logout">
			<a href="/logout"><span class="icon-exit"></span></a>
		</div>
	</header>

	<div class="wrapper">
		<?=$menu?>
		<div class="content">
			<div class="content_inner">
				<?if(!empty($errors)){?>
					<script>
					<?foreach($errors as $error){
						echo 'message(0,"'.$error.'");';
					}?>
					</script>
				<?}?>
				<?=$content?>
			</div>
		</div>
	</div>

    <?if (!empty($popupGlobalMessages)){?>
        <?=$popupGlobalMessages?>
        <script>
            $(function () {
                $.fancybox({
                    href: '#common_global_messages',
                    modal: true,
                    padding: [0,0,0,0]
                });
            });
        </script>
    <?}?>

	<!--[if (lt IE 9)]>
	<div class="bad_browser">
		<h1>Ваш браузер устарел</h1>
		Установите современный браузер <a href="http://www.firefox.com">Firefox</a>, <a href="http://www.google.com/chrome/">Chrome</a>, <a href="http://www.opera.com">Opera</a>.
	</div>
	<![endif]-->
</body>
</html>
