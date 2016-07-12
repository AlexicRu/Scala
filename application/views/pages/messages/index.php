<h1>Сообщения</h1>

<div class="block">
    <div class="ajax_block_messages_out">

    </div>
</div>

<script>
    $(function(){
        paginationAjax('/messages/', 'ajax_block_messages', renderAjaxPaginationMessages);
    });

    function renderAjaxPaginationMessages(data, block) {
        for(var i = 0 in data){
            var tpl = $('<div class="notice"><div class="n_title"></div></div>');
            tpl.find('.n_title').text(data[i]['SUBJECT']);
            tpl.append(data[i]['NOTIFICATION_BODY']);
            block.append(tpl);
        }
    }
</script>