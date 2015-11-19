<?
$cntWork = 0;
$cntDisable = 0;

foreach($cards as $card){
    if($card['CARD_STATE'] != Model_Card::CARD_STATE_BLOCKED){
        $cntWork++;
    }else{
        $cntDisable++;
    }
}
?>

<div class="tc_top_line">
    <span class="gray">Всего карт:</span> <?=count($cards)?> &nbsp;&nbsp;&nbsp;
    <span class="gray">В работе:</span> <span class="cards_cnt_in_work"><?=$cntWork?></span> &nbsp;&nbsp;&nbsp;
    <span class="red">Заблокировано: <span class="cards_cnt_blocked"><?=$cntDisable?></span></span>
    <div class="fr input_with_icon"><i class="icon-find"></i><input type="text" class="input_big cards_search" placeholder="Поиск..." value="<?=$query?>"></div>
</div>
<div class="tabs_vertical_block tabs_switcher tabs_cards">
    <div class="tabs_v">
        <?if(Access::allow('clients_card_add')){?>
            <div class="tab_v"><div>
                <a href="#card_add" class="fancy"><span class="icon-card"></span> Добавить карту</a>
            </div></div>
        <?}?>
        <div class="scroll">
            <?foreach($cards as $key => $card){?>
                <div class="tab_v" tab="<?=$card['CARD_ID']?>"><div>
                    <span class="icon-card gray"></span>
                    <?=$card['CARD_ID']?>
                    <div class="gray"><?=$card['HOLDER']?></div>
                    <?if($card['CARD_STATE'] == Model_Card::CARD_STATE_BLOCKED){?>
                        <span class="label label_error label_small">Заблокирована</span>
                    <?}?>
                </div></div>
            <?}?>
        </div>
        <!--div class="tab_v gray preload"><div>
                <span class="icon-loader"></span> Загрузка карточек
            </div></div-->
    </div>
    <div class="tabs_v_content">
        <?foreach($cards as $key => $card){?>
            <div class="tab_v_content" tab_content="<?=$card['CARD_ID']?>"></div>
        <?}?>
    </div>
</div>

<?if(Access::allow('clients_card_add')){?>
    <?=$popupCardAdd?>
<?}?>

<script>
    $(function(){
        $(".tabs_cards [tab]").on('click', function(){
            var t = $(this);

            if($(".tabs_cards [tab_content="+ t.attr('tab') +"]").text() == ''){
                $(".tabs_cards [tab_content="+ t.attr('tab') +"]").addClass('block_loading');

                $.post('/clients/card/' + t.attr('tab'), {}, function(data){
                    $(".tabs_cards [tab_content="+ t.attr('tab') +"]").html(data).removeClass('block_loading');
                });
            }
        });

        $(".tabs_cards [tab]:first").click();

        $(".cards_search").on('keypress', function(e){
            if(e.keyCode == 13){
                loadContract('cards', $(".cards_search").val());
            }
        });

        <?if(Access::allow('clients_card_add')){?>
            renderScroll($('.tabs_cards .scroll'), -70);
        <?}else{?>
            renderScroll($('.tabs_cards .scroll'));
        <?}?>

        $(document).on('click', '.btn_card_toggle', function(){
            var t = $(this);

            var comment = '';

            if(t.hasClass('btn_red')){
                comment = prompt('Причина блокировки:');
            }

            if(comment != null) {
                var params = {
                    card_id: $('.tab_v_content.active [name=card_id]').val(),
                    comment: comment
                };

                $.post('/clients/card_toggle', {params:params}, function (data) {
                    if (data.success) {
                        t.toggleClass('btn_red').toggleClass('btn_green').find('span').toggle();

                        var tab = $('.tabs_cards [tab='+ params.card_id +'] > div');
                        var cnt_in_work = $('.cards_cnt_in_work');
                        var cnt_blocked = $('.cards_cnt_blocked');

                        if(t.hasClass('btn_green')){
                            tab.append('<span class="label label_error label_small">Заблокирована</span>');
                            cnt_in_work.text(parseInt(cnt_in_work.text()) - 1);
                            cnt_blocked.text(parseInt(cnt_blocked.text()) + 1);
                        }else{
                            tab.find('.label_error').remove();
                            cnt_in_work.text(parseInt(cnt_in_work.text()) + 1);
                            cnt_blocked.text(parseInt(cnt_blocked.text()) - 1);
                        }

                        message(1, 'Статус карты изменен');
                    } else {
                        message(0, 'Ошибка обновления');
                    }
                });
            }
        });
    });
</script>