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
			<div class="content_inner">
				<h1>Авторизация</h1>
				<div class="block">
					<form method="post" action="/login">
						<input type="text" placeholder="Логин" name="login" class="input_big">
						<input type="password" placeholder="Пароль" name="password" class="input_big">
						<button class="btn">Войти</button>
					</form>
				</div>

				<?
				$messages = Messages::get();
				if(!empty($messages)){
					foreach($messages as $message){
						echo '<div class="message message-'.$message['type'].'">'.$message['text'].'<span class="message_close icon-cancel"></span></div>';
					}
				}
				?>
			</div>
		</div>
	</div>
</body>
</html>
