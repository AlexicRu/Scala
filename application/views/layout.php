<!DOCTYPE html>
<html lang="ru-RU">
<head>
	<meta charset="UTF-8">

	<title>СКАЛА</title>

	<script src="http://yastatic.net/jquery/2.1.3/jquery.min.js"></script>

	<link href="/style.css" rel="stylesheet">

	<script src="/js/site.js"></script>

	<!--[if lt IE 9]>
	<style>
		.bad_browser{display:block;}
		.wrapper, header{display:none;}
	</style>
	<![endif]-->
</head>

<body>
<header>
	<div class="logo">
		<a href="/"><img src="/img/logo.png"></a>
	</div>
	<div class="hamburger">
		<a href="#"><span class="icon-menu"></span></a>
	</div>
	<div class="search">
		<button class="icon-find"></button><input type="text" placeholder="Поиск...">
	</div>
	<div class="personal">
		<div class="avatar" style="background-image: url(/img/pic/01.png)"></div> Константинов Сергей Николавевич
	</div>
	<div class="mail">
		<a href="#"><span class="icon-mail"><span>6</span></span></a>
	</div>
	<div class="settings">
		<a href="#"><span class="icon-gear"></span></a>
	</div>
	<div class="logout">
		<a href="#"><span class="icon-exit"></span></a>
	</div>
</header>

<div class="wrapper">
	<?=$menu?>
	<div class="content">
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
