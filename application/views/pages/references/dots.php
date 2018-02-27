<h1>Точки обслуживания</h1>

<div class="block no_padding dots_info">
    <div class="tab_content_header">
        <div class="fr">
            <span class="btn btn_green btn_icon" onclick="dotsInfoToXls()"><i class="icon-exel1"></i> Выгрузить</span>
        </div>
        <br class="clr">
    </div>

    <div class="list"></div>
</div>

<script>
    $(function(){
        showDotsList();
    });

    function showDotsList()
    {
        var block = $('.dots_info .list');

        if(block.html() != ''){
            return true;
        }

        block.addClass('block_loading');

        $.post('/control/show-dots', { postfix: 'dots_info' }, function (data) {
            block.removeClass('block_loading');

            block.html(data);
        });
    }

    function dotsInfoToXls()
    {
        var pos_id = [];

        $('.ajax_block_dots_list_dots_info [pos_id]').each(function () {
            pos_id.push($(this).attr('pos_id'));
        });

        window.open('/control/load-dots?to_xls=1&pos_id=' + pos_id.join(','));
    }
</script>