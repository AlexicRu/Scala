<!DOCTYPE html>
<html lang="ru-RU">
<head>
	<meta charset="UTF-8">

	<title><?=$title?></title>

	<link href="/style.css" rel="stylesheet">

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
		<!--div class="hamburger">
			<a href="#"><span class="icon-menu"></span></a>
		</div-->
		<div>&nbsp;</div>
	</header>

	<div class="wrapper">
		<?/*=$menu*/?>
		<div class="content">
			<h1>Авторизация</h1>
			<div class="block">
				<form method="post" action="/login">
					<input type="text" placeholder="Логин" name="login" class="input_big">
					<input type="password" placeholder="Пароль" name="password" class="input_big">
					<button class="btn">Войти</button>
				</form>
			</div>
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
