<h1>Сообщения</h1>

<div class="block list">
    <div class="list_header">
        <div><a href="#" class="mark_read"><i class="icon-ok"></i> Отметить прочитанным</a></div>
        <div><div class="fr input_with_icon"><i class="icon-find"></i><form><input type="text" name="m_search" class="input_big input_messages" placeholder="Поиск..." value="<?=$mSearch?>"></form></div></div>
    </div>
    <div class="ajax_block_messages_out">

    </div>
</div>

<script>
    $(function(){
        paginationAjax('/messages/', 'ajax_block_messages', renderAjaxPaginationMessages, {'search': '<?=$mSearch?>'});

        $('.input_messages').on('keypress', function (e) {
            if(e.keyCode == 13) {
                var t = $(this);
                var form = t.closest('form');

                form.submit();
            }
        });
    });

    function renderAjaxPaginationMessages(data, block) {
        for(var i = 0 in data){
            var tpl = $('<div class="news_elem"><div class="n_title"></div><div class="n_date gray"></div></div>');
            tpl.find('.n_title').text(data[i]['SUBJECT']);
            tpl.find('.n_date').text(data[i]['NOTE_DATE']);
            tpl.append(data[i]['NOTIFICATION_BODY']);
            tpl.addClass('unread' + data[i].STATUS);
            block.append(tpl);
        }
    }
</script>