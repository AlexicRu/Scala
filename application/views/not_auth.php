<!DOCTYPE html>
<html lang="ru-RU">
<head>
	<meta charset="UTF-8">

	<title><?=$title?></title>

	<?foreach($styles as $style){?>
		<link href="<?=$style?>" rel="stylesheet">
	<?}?>
</head>

<body class="custom_view_<?=$customView?>">
	<header>
		<div class="logo">
			<a href="/"></a>
		</div>
		<div>&nbsp;</div>
	</header>

	<div class="wrapper">
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
</body>
</html>
