<div class="global_messages">
    <?foreach ($globalMessages as $message){?>
        <div class="news_elem">
            <div class="n_title"><?=$message['SUBJECT']?></div>
            <div class="n_date gray"><?=$message['NOTE_DATE']?></div>
            <?=$message['NOTIFICATION_BODY']?>
        </div>
    <?}?>
</div>

<div class="center">
    <span class="btn btn_orange btn_reverse" onclick="globalMessagesMarkAsRead($(this))"><i class="icon-ok"></i> Закрыть</span>
</div>

<script>
    function globalMessagesMarkAsRead() {
        //$.post('/messages/make_read', {type: <?=Model_Message::MESSAGE_TYPE_GLOBAL?>}, function () {
            $.fancybox.close();
        //})
    }
</script>