<div class="ajax_block_cards_group_<?=$groupId?>_out">

</div>

<script>
    $(function(){
        paginationAjax('/control/load_group_cards/', 'ajax_block_cards_group_<?=$groupId?>', renderAjaxPaginationCardsGroup<?=$groupId?>, {group_id: <?=$groupId?>, can_edit:<?=(int)$canEdit?>});
    });

    function renderAjaxPaginationCardsGroup<?=$groupId?>(data, block, params)
    {
        var canEdit = false;

        if(params.can_edit){
            canEdit = true;
        }

        if(block.find('> table').size() == 0){
            block.append('<table class="table table_small table_fullscreen check_all_block"></table>');
            block = block.find('table');

            block.append('<tr>' +
                (canEdit ? '<th class="td_check"><input type="checkbox" onchange="checkAllRows($(this), \'card_id\')"></th>' : '') +
                '<th><nobr>CARD ID</nobr></th>' +
                '<th>Владелец</th>' +
                '<th>Описание</th>' +
                (canEdit ? '<th class="td_edit"></th>' : '') +
            '</tr>');
            renderCheckbox(block.find('.td_check [type=checkbox]'));
        }else{
            block = block.find('table');
        }

        for(var i in data){
            var tpl = $('<tr class="card_row">' +
                (canEdit ? '<td class="td_check" />' : '') +
                '<td class="group_card_td_CARD_ID" />' +
                '<td class="group_card_td_HOLDER" />' +
                '<td class="group_card_td_DESCRIPTION_RU" />' +
                (canEdit ? '<td class="td_edit"/>' : '') +
            '</tr>');

            tpl.attr('id', data[i].CARD_ID);
            tpl.find('.td_check').html('<input type="checkbox" name="card_id" value="'+ data[i].CARD_ID +'">');
            tpl.find('.group_card_td_CARD_ID').text(data[i].CARD_ID);
            tpl.find('.group_card_td_HOLDER').text(data[i].HOLDER);
            tpl.find('.group_card_td_DESCRIPTION_RU').text(data[i].DESCRIPTION_RU);
            tpl.find('.td_edit').html('<span class="btn btn_green btn_small btn_icon"><i class="icon-pen"></span>');

            block.append(tpl);
            renderCheckbox(tpl.find('.td_check [type=checkbox]'));
        }

        if($('.tabs_cards_groups .action_del').is(':visible')){
            $('.td_check, .td_edit').show();
        }

        renderScroll($('.tabs_cards_groups .scroll'));
    }
</script>