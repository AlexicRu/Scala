<div class="tc_top_line">
    <span class="gray">Всего карт:
        <a href="#" onclick="filterCards('all')"><?=$cntCards?></a>
    </span> &nbsp;&nbsp;&nbsp;
    <span class="gray <?=(!empty($params['status']) && $params['status'] == 'work' ? 'act' : '')?>">
        В работе: <a href="#" onclick="filterCards(true)" class="cards_cnt_in_work"><?=$cntWork?></a>
    </span> &nbsp;&nbsp;&nbsp;
    <span class="red <?=(!empty($params['status']) && $params['status'] == 'disabled' ? 'act' : '')?>">
        Заблокировано: <a href="#" onclick="filterCards(false)" class="cards_cnt_blocked"><?=$cntDisable?></a>
    </span>
    <div class="fr input_with_icon"><i class="icon-find"></i><input type="text" class="input_big cards_search" placeholder="Поиск..." value="<?=(!empty($params['query']) ? $params['query'] : '')?>"></div>
</div>
<div class="tabs_vertical_block tabs_switcher tabs_cards">
    <div class="tabs_v">
        <?if(Access::allow('clients_card_add')){?>
            <div class="before_scroll">
                <div class="tab_v"><div>
                    <a href="#card_add" class="fancy"><span class="icon-card"></span> Добавить карту</a>
                </div></div>
            </div>
        <?}?>
        <div class="scroll">
            <?include('cards/list.php')?>
        </div>
    </div>
    <div class="tabs_v_content"></div>
</div>

<?if(Access::allow('clients_card_add')){?>
    <?=$popupCardAdd?>
<?}?>

<script>
    $(function(){
/*
 0 - не отображать кнопку "Блокировать"/"Разблокировать"
 1 - отображать кнопку "Блокировать"/"Разблокировать",
 2 - отображать, но при блокировании такой карты писать: "Заявка на блокировку/разблокировку отправлена менеджеру. Карта будет заблокирована/разблокирована в течение 48 часов!" (писать блокирока или разблокировка в зависимости от действия)
 при изъятии такой карты писать: "Карта откреплена от договора! Проверьте статус в сторонней системе!"
* */
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

                        if(t.hasClass('btn_green')){
                            tab.append('<span class="label label_error label_small">Заблокирована</span>');
                            cnt_in_work.text(parseInt(cnt_in_work.text()) - 1);
                            cnt_blocked.text(parseInt(cnt_blocked.text()) + 1);

                            var blockAvailableText = 'Заявка на блокировку отправлена менеджеру. Карта будет заблокирована в течение 48 часов!';
                        }else{
                            tab.find('.label_error').remove();
                            cnt_in_work.text(parseInt(cnt_in_work.text()) + 1);
                            cnt_blocked.text(parseInt(cnt_blocked.text()) - 1);

                            var blockAvailableText = 'Заявка на разблокировку отправлена менеджеру. Карта будет разблокирована в течение 48 часов!';
                         }

                        message(1, t.attr('block_available') == 2 ? blockAvailableText : 'Статус карты изменен');
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
            tpl.find('div.fr').html(data[i].SHORT_DESCRIPTION);

            if(data[i].DESCRIPTION){
                tpl.append('<div class="full_comment">Комментарий: '+ data[i].DESCRIPTION +'</div>');
            }

            block.append(tpl);
        }

        renderScroll($('.tabs_cards .scroll'));
    }

    /**
     * изъятие карты
     * @param cardId
     */
    function cardWithdraw(cardId, blockAvailable)
    {
        if(!confirm('Изъять карту из договора?')){
            return false;
        }
        var params = {
            card_id: cardId,
            contract_id: $('[name=contracts_list]').val()
        };

        $.post('/clients/card_withdraw', {params:params}, function (data) {
            if (data.success) {

                message(1, blockAvailable == 2 ? 'Карта откреплена от договора! Проверьте статус в сторонней системе!' : 'Успешное изъятие');
                loadContract('cards');
            } else {
                message(0, 'Ошибка изъятия');
            }
        });
        return false;
    }

    /**
     * фильтр
     */
    function filterCards(type)
    {
        if(type == 'all') {
            loadContract('cards', $(".cards_search").val());

            return false;
        }

        loadContract('cards', $(".cards_search").val(), {status: type == 1 ? 'work' : 'disabled'});

        return false;
    }
</script>