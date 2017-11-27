<div class="ajax_block_dots_<?=$groupId?>_out">

</div>

<script>
    $(function(){
        paginationAjax('/control/load_group_dots/', 'ajax_block_dots_<?=$groupId?>', renderAjaxPaginationDots<?=$groupId?>, {group_id: <?=$groupId?>, can_edit:<?=(int)$canEdit?>});
    });

    function renderAjaxPaginationDots<?=$groupId?>(data, block, params)
    {
        var canEdit = false;

        if(params.can_edit){
            canEdit = true;
        }

        if(block.find('> table').size() == 0){
            block.append('<table class="table table_small table_fullscreen check_all_block"></table>');
            block = block.find('table');

            block.append('<tr>' +
                (canEdit ? '<th class="td_check"><input type="checkbox" onchange="checkAllRows($(this), \'pos_id\')"></th>' : '') +
                '<th><nobr>Шаблон ТО</nobr></th>' +
                '<th><nobr>Эмитент</nobr></th>' +
                '<th><nobr>Номер ТО</nobr></th>' +
                '<th><nobr>Название</nobr></th>' +
                '<th>Владелец</th>' +
                '<th>Адрес</th>' +
                (canEdit ? '<th class="td_edit"></th>' : '') +
            '</tr>');
            renderCheckbox(block.find('.td_check [type=checkbox]'));
        }else{
            block = block.find('table');
        }

        for(var i in data){
            var tpl = $('<tr class="dot_row">' +
                (canEdit ? '<td class="td_check" />' : '') +
                '<td class="dot_td_project_name" />' +
                '<td class="dot_td_id_emi" />' +
                '<td class="dot_td_id_to" />' +
                '<td class="dot_td_pos_name"/>' +
                '<td class="dot_td_owner"/>' +
                '<td class="dot_td_address"/>' +
                (canEdit ? '<td class="td_edit"/>' : '') +
            '</tr>');

            tpl.attr('id', data[i].POS_ID);
            tpl.find('.td_check').html('<input type="checkbox" name="pos_id" value="'+ data[i].POS_ID +'">');
            tpl.find('.dot_td_project_name').text(data[i].PROJECT_NAME);
            tpl.find('.dot_td_id_emi').text(data[i].ID_EMITENT);
            tpl.find('.dot_td_id_to').text(data[i].ID_TO);
            tpl.find('.dot_td_pos_name').text(data[i].POS_NAME);
            tpl.find('.dot_td_owner').text(data[i].OWNER);
            tpl.find('.dot_td_address').text(data[i].POS_ADDRESS);
            tpl.find('.td_edit').html('<span class="btn btn_green btn_small btn_icon"><i class="icon-pen"></span>');

            block.append(tpl);
            renderCheckbox(tpl.find('.td_check [type=checkbox]'));
        }

        if($('.tabs_group_dots .action_del').is(':visible')){
            $('.td_check, .td_edit').show();
        }

        renderScroll($('.tabs_group_dots .scroll'));
    }
</script>