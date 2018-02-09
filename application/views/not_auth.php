<!DOCTYPE html>
<html lang="ru-RU">
<head>

    <?=(!empty($favicon) ? $favicon : '')?>

	<meta charset="UTF-8">

	<title><?=(!empty($title) ? $title : '')?></title>

    <?if (!empty($styles)) {
    	foreach($styles as $style){?>
		    <link href="<?=$style?>?t=<?=Common::getSalt('css')?>" rel="stylesheet">
	    <?}
    }?>
	<?if (!empty($scripts)) {
        foreach($scripts as $script){?>
            <script src="<?=$script?>?t=<?=Common::getSalt('js')?>"></script>
        <?}
    }?>
</head>

<body class="custom_view_<?=(!empty($customView) ? $customView : 'glopro')?>">
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
					<form id="login" method="post" action="/login">
						<input type="text" placeholder="Логин" name="login" class="input_big">
						<input type="password" placeholder="Пароль" name="password" class="input_big">

                        <?
                        $config = Kohana::$config->load('config');
                        ?>

                        <button
                                class="g-recaptcha btn"
                                data-sitekey="<?=$config['recaptcha_public']?>"
                                data-callback="onSubmit">
                            Войти
                        </button>
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

    <script>
        function onSubmit(token) {
            $('#login').submit();
        }
    </script>
</body>
</html>
