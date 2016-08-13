<h1>
    Новости

    <?if(Access::allow('news_news_add')){?>
        <a href="#news_add" class="btn fancy">Добавить новость</a>
    <?}?>
</h1>

<div class="block list">
    <div class="ajax_block_news_out">

    </div>
</div>

<?if(Access::allow('news_news_add')){?>
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
            tpl.find('.n_title').text(data[i]['SUBJECT']).attr('href', '/news/' + data[i].ID);
            tpl.find('.n_date').text(data[i]['NOTE_DATE']);
            tpl.find('.n_link a').attr('href', '/news/' + data[i].ID);
            tpl.find('.n_body').text(data[i]['NOTIFICATION_BODY']);
            if(data[i].IMG) {
                tpl.find('.n_img').css('background-image', 'url('+ data[i].IMG +')');
            }else{
                tpl.find('.n_img').remove();
            }
            block.append(tpl);
        }
    }
</script>