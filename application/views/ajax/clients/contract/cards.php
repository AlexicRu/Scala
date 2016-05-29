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
            <?if(is_array($foundCards) && empty($foundCards)){?>
                <span class="gray">Карты не анйдены</span>
            <?}else{?>
                <?foreach($cards as $key => $card){
                    $found = true;
                    if($foundCards !== false){
                        $found = false;
                        foreach($foundCards as $foundCard){
                            if($foundCard['CARD_ID'] == $card['CARD_ID']){
                                $found = true;
                                break;
                            }
                        }
                    }
                    ?>
                    <div class="tab_v <?if($card['CARD_STATE'] == Model_Card::CARD_STATE_BLOCKED){?>card_blocked<?}?>" tab="<?=$card['CARD_ID']?>" <?if(empty($found)){?>style="display: none;"<?}?>><div>
                            <span class="icon-card gray"></span>
                            <?=$card['CARD_ID']?>
                            <div class="gray"><?=$card['HOLDER']?></div>
                        </div></div>
                <?}?>
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

            cardLoad(t);
        });

        var clicked = false;
        $(".tabs_cards [tab]").each(function(){
            if(clicked){
                return;
            }
            var t = $(this);

            if(!t.attr('style')){
                clicked = true;
                t.click();
            }
        });

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

        $(document).off('click', '.btn_card_toggle').on('click', '.btn_card_toggle', function(){
            var t = $(this);

            var comment = '';

            if(t.hasClass('btn_red')){
                comment = prompt('Причина блокировки:');
            }

            if(comment != null) {
                var params = {
                    card_id: $('.tab_v_content.active [name=card_id]').val(),
                    contract_id: $('[name=contracts_list]').val(),
                    comment: comment
                };

                $.post('/clients/card_toggle', {params:params}, function (data) {
                    if (data.success) {
                        t.toggleClass('btn_red').toggleClass('btn_green').find('span').toggle();

                        var tab = $('.tabs_cards [tab='+ params.card_id +'] > div');
                        var cnt_in_work = $('.cards_cnt_in_work');
                        var cnt_blocked = $('.cards_cnt_blocked');

                        tab.parent().toggleClass('card_blocked');
                        if(t.hasClass('btn_green')){
                            cnt_in_work.text(parseInt(cnt_in_work.text()) - 1);
                            cnt_blocked.text(parseInt(cnt_blocked.text()) + 1);
                        }else{
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

    function renderAjaxPaginationOperationsHistory(data, block)
    {
        for(var i = 0 in data){
            var tpl = $('<div class="line_inner"><span class="gray" /> &nbsp;&nbsp;&nbsp; <span /><div class="fr" /></div>');
            tpl.find('span.gray').text(data[i].H_DATE);
            tpl.find('span:last').text(data[i].M_FIO);
            tpl.find('div.fr').text(data[i].SHORT_DESCRIPTION);
            block.append(tpl);
        }
    }
</script>