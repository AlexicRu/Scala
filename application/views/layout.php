<!DOCTYPE html>
<html lang="ru-RU">
<head>
	<meta charset="UTF-8">

	<title><?=$title?></title>

	<?foreach($styles as $style){?>
		<link href="<?=$style?>" rel="stylesheet">
	<?}?>
	<?foreach($scripts as $script){?>
		<script src="<?=$script?>"></script>
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
			<a href="#"><span class="icon-menu"></span></a>
		</div>
		<div class="search">
			<form action="/clients" method="post"><button class="icon-find"></button><input type="text" name="search" placeholder="Поиск..." value="<?=(!empty($_REQUEST['search']) ? $_REQUEST['search'] : '')?>"></form>
		</div>
		<div class="personal">
			<div class="avatar" <?/*style="background-image: url(/img/pic/01.png)"*/?>><i class="icon-user"></i></div>
			<?
			if(!empty($user['MANAGER_NAME']) && !empty($user['MANAGER_SURNAME']) && !empty($user['MANAGER_MIDDLENAME'])){
				echo $user['MANAGER_NAME'].' '.$user['MANAGER_SURNAME'].' '.$user['MANAGER_MIDDLENAME'];
			}elseif(!empty($user['FIRM_NAME'])){
				echo $user['FIRM_NAME'];
			}else{
				echo $user['LOGIN'];
			}
			?>
		</div>
		<div class="mail">
			<a href="/messages"><span class="icon-mail"><?if(count($notices)){?><span><?=count($notices)?></span><?}?></span></a>
			<?if(count($notices)){?>
				<div class="notices">
					<?foreach($notices as $notice){?>
						<div class="notice">
							<div class="n_title"><?=$notice[0]?></div>
							<?=$notice[1]?>
						</div>
					<?}?>
					<div>
						<a href="#" class="mark_read">Отметить прочитанным</a>
					</div>
				</div>
			<?}?>
		</div>
		<div class="settings">
			<a href="/customer/settings"><span class="icon-gear"></span></a>
		</div>
		<div class="logout">
			<a href="/logout"><span class="icon-exit"></span></a>
		</div>
	</header>

	<div class="wrapper">
		<?=$menu?>
		<div class="content">
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
	<!--[if (lt IE 9)]>
	<div class="bad_browser">
		<h1>Ваш браузер устарел</h1>
		Установите современный браузер <a href="http://www.firefox.com">Firefox</a>, <a href="http://www.google.com/chrome/">Chrome</a>, <a href="http://www.opera.com">Opera</a>.
	</div>
	<![endif]-->
</body>
</html>
