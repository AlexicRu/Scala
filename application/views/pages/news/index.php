<h1>
    Новости

    <?if(Access::allow('news_news_edit')){?>
        <a href="#news_edit" class="btn fancy">Добавить новость</a>
    <?}?>
</h1>

<div class="block list">
    <div class="ajax_block_news_out">

    </div>
</div>

<?if(Access::allow('news_news_edit')){?>
    <?=$popupNewsAdd?>
<?}?>

<script>
    $(function(){
        paginationAjax('/news/', 'ajax_block_news', renderAjaxPaginationNews);
    });

    function renderAjaxPaginationNews(data, block)
    {
        for(var i in data){
            var tpl = $('<div class="news_elem"><div class="n_img" /><a class="n_title"></a><div class="n_date gray"></div><div class="n_body" /><div class="n_link"><a>Читать подробнее</a></div></div>');
            if(data[i]['AGENT_ID'] == 0){
                tpl.prepend('<i class="icon-user"></i> ');
            }
            tpl.find('.n_title').text(data[i]['TITLE']).attr('href', '/news/' + data[i].NEWS_ID);
            tpl.find('.n_date').text(data[i]['DATE_CREATE_WEB']);
            tpl.find('.n_link a').attr('href', '/news/' + data[i].NEWS_ID);
            tpl.find('.n_body').html(data[i]['announce']);
            if(data[i].PICTURE) {
                tpl.find('.n_img').css('background-image', 'url('+ data[i].PICTURE +')');
            }else{
                tpl.find('.n_img').remove();
            }
            block.append(tpl);
        }
    }
</script>