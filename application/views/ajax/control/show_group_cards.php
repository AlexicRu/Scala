<?if(!empty($showCheckbox)){?>
    <input type="hidden" name="show_checkboxes<?=$postfix?>" value="1">
<?}?>
<?if(!empty($groupId)){?>
    <input type="hidden" name="group_id_<?=$postfix?>" value="<?=$groupId?>">
<?}?>
<div class="ajax_block_group_cards_list_<?=$postfix?>_out">

</div>

<script>
    $(function(){
        var params = {};

        if($('[name=group_id_<?=$postfix?>]').length){
            params.group_id = $('[name=group_id_<?=$postfix?>]').val();
        }

        paginationAjax('/control/load_cards/', 'ajax_block_group_cards_list_<?=$postfix?>', renderAjaxPaginationGroupCardsList<?=$postfix?>, params);
    });

    function renderAjaxPaginationGroupCardsList<?=$postfix?>(data, block, params)
    {
        if(block.find('> table').size() == 0){
            block.append('<table class="table table_small table_fullscreen check_all_block"></table>');
            block = block.find('table');

            block.append('<tr>' +
                '<th class="td_check"><input type="checkbox" onchange="checkAllRows($(this), \'card_id\')" style="display: none;"></th>' +
                '<th><input type="text" name="group_card_filter_card_id" placeholder="CARD ID" class="input_small"></th>' +
                '<th><input type="text" name="group_card_filter_holder" placeholder="Владелец"></th>' +
                '<th style="width:400px;"><input type="text" name="group_card_filter_description_ru" placeholder="Описание"></th>' +
                '<th class="td_edit">' +
                    '<button class="btn btn_small btn_icon fr" onclick="filterGroupCards<?=$postfix?>($(this))"><i class="icon-find"></i></button>'+
                '</th>' +
            '</tr>');
            renderCheckbox(block.find('.td_check [type=checkbox]'));
        }

        if(params.CARD_ID){
            block.find('[name=group_card_filter_card_id]').val(params.CARD_ID);
        }
        if(params.HOLDER){
            block.find('[name=group_card_filter_holder]').val(params.HOLDER);
        }
        if(params.DESCRIPTION_RU){
            block.find('[name=group_card_filter_description_ru]').val(params.DESCRIPTION_RU);
        }

        var subBlock = block.find('tbody');

        for(var i in data){
            var tpl = $('<tr>' +
                '<td class="td_check" />' +
                '<td class="group_card_td_CARD_ID" />' +
                '<td class="group_card_td_HOLDER" />' +
                '<td class="group_card_td_DESCRIPTION_RU" />' +
                '<td class="td_edit"/>' +
            '</tr>');

            tpl.find('.td_check').html('<input type="checkbox" name="card_id" value="'+ data[i].CARD_ID +'">');
            tpl.find('.group_card_td_CARD_ID').text(data[i].CARD_ID);
            tpl.find('.group_card_td_HOLDER').text(data[i].HOLDER);
            tpl.find('.group_card_td_DESCRIPTION_RU').text(data[i].DESCRIPTION_RU);
            tpl.find('.td_edit').html('<span class="btn btn_green btn_small btn_icon"><i class="icon-pen"></span>');

            subBlock.append(tpl);
            renderCheckbox(tpl.find('.td_check [type=checkbox]'));
        }

        if($('.tabs_cards_groups .action_del', block).is(':visible')){
            $('.td_edit', block).show();
        }
        if($('.tabs_cards_groups .action_del', block).is(':visible') || $('[name=show_checkboxes<?=$postfix?>]').length){
            $('.td_check', block).show();
        }
    }
    
    function filterGroupCards<?=$postfix?>(btn)
    {
        var block = btn.closest('.ajax_block_group_cards_list_<?=$postfix?>_out');

        var params = {
            CARD_ID:            $('[name=group_card_filter_project_name]', block).val(),
            HOLDER:             $('[name=group_card_filter_id_emi]', block).val(),
            DESCRIPTION_RU:     $('[name=group_card_filter_id_to]', block).val(),
        };

        if($('[name=group_id_<?=$postfix?>]').length){
            params.group_id = $('[name=group_id_<?=$postfix?>]').val();
        }

        block.empty().addClass('block_loading');

        paginationAjax('/control/load_cards/', 'ajax_block_group_cards_list_<?=$postfix?>', renderAjaxPaginationGroupCardsList<?=$postfix?>, params);
    }
</script>