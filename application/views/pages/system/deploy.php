<h1>Deploy</h1>

<div class="block">
    <a href="/system/full" class="ajax btn_orange btn_reverse btn">Full rebuild</a> - gulp build, version refresh and git

    <div class="result"></div>
</div>

<h2>Версия</h2>
<div class="block">
    <a href="/system/version-refresh" class="ajax btn">Обновить</a>

    <div class="result">
        <pre><?=$version?></pre>
    </div>
</div>

<h2>Сборка frontend</h2>

<div class="block">
    <a href="/system/gulp/build" class="btn ajax"><b>gulp build</b> - css, js, fonts, image</a> &nbsp;&nbsp;&nbsp;
    <a href="/system/gulp/fast" class="btn ajax"><b>gulp fast</b> - css, js</a> &nbsp;&nbsp;&nbsp;
    <a href="/system/gulp/images" class="btn ajax"><b>gulp images</b></a>

    <div class="result"></div>
</div>

<h2>Сборка backend</h2>

<div class="block">
    <a href="/system/deploy" class="ajax btn"><b>deploy</b> - git</a>

    <div class="result"></div>
</div>

<script>
    $('.ajax').on('click', function () {
        var t = $(this);
        var result = t.closest('.block').find('.result');

        result.empty().addClass(CLASS_LOADING);

        $.post(t.attr('href'), {}, function (html) {
            result.removeClass(CLASS_LOADING).html('<pre>' + html + '</pre>');
        });

        return false;
    });
</script>