<!DOCTYPE html>
<html lang="ru-RU">
<head>
    <?=Common::getFaviconRawData()?>

	<meta charset="UTF-8">
	<title>403 - доступ запрещен</title>

	<link href="<?=Common::getAssetsLink()?>css/style.css" rel="stylesheet">
</head>

<body class="custom_view_<?=$customView?>">
	<div class="wrapper">
		<div class="content">
			<h1>403 - доступ запрещен</h1><br>
			<a href="/">На главную</a> или <a href="javascript:history.go(-1);">назад</a>
		</div>
	</div>
</body>
</html>
