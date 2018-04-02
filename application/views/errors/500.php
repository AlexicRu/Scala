<!DOCTYPE html>
<html lang="ru-RU">
<head>
    <?=Common::getFaviconRawData()?>

	<meta charset="UTF-8">
	<title>500 - ошибка сервера</title>

	<link href="<?=Common::getAssetsLink()?>css/style.css" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
		<div class="content">
			<h1>500 - ошибка сервера</h1><br>
			<?=$message?><br><br>
			<a href="/">На главную</a> или <a href="javascript:history.go(-1);">назад</a>
		</div>
	</div>
</body>
</html>
