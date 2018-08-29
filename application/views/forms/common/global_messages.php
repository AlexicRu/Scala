<div class="global_messages">
    <?foreach ($globalMessages as $message){?>
        <div class="news_elem">
            <div class="n_title"><?=$message['NOTE_TITLE']?></div>
            <div class="n_date gray"><?=$message['NOTE_DATE']?></div>
            <?=$message['NOTE_ID']?>
            <?=$message['NOTE_BODY']?>
        </div>
    <?}?>
</div>

<div class="center">
    <span class="btn btn_orange btn_reverse" onclick="globalMessagesMarkAsRead($(this))"><i class="icon-ok"></i> Закрыть</span>
</div>

<script>
    function globalMessagesMarkAsRead() {
        //$.post('/messages/make-read', {type: <?=Model_Note::NOTE_TYPE_POPUP?>}, function () {
            $.fancybox.close();
        //})
    }
</script>