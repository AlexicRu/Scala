<?if(!empty($showCheckbox)){?>
    <input type="hidden" name="show_dots_<?=$postfix?>" value="1">
<?}?>
<?if(!empty($groupId)){?>
    <input type="hidden" name="group_id_<?=$postfix?>" value="<?=$groupId?>">
<?}?>
<div class="ajax_block_dots_list_<?=$postfix?>_out">

</div>

<script>
    $(function(){
        var params = {
            show_all_btn:true
        };

        if($('[name=group_id_<?=$postfix?>]').length){
            params.group_id = $('[name=group_id_<?=$postfix?>]').val();
        }

        paginationAjax('/control/load-dots/', 'ajax_block_dots_list_<?=$postfix?>', renderAjaxPaginationDotsList<?=$postfix?>, params);
    });

    function renderFilterDotsList<?=$postfix?>(block, params)
    {
        if(block.find('> table').size() == 0){
            block.append('<table class="table table_small table_fullscreen check_all_block"></table>');
            block = block.find('table');

            block.append('<tr>' +
                '<th class="td_check"><input type="checkbox" onchange="checkAllRows($(this), \'pos_id\')" style="display: none;"></th>' +
                '<th><input type="text" name="dots_filter_project_name" placeholder="Шаблон ТО" class="input_small"></th>' +
                '<th><input type="text" name="dots_filter_id_emi" placeholder="Эмитент" class="input_small"></th>' +
                '<th><input type="text" name="dots_filter_id_to" placeholder="Номер ТО" class="input_small"></th>' +
                '<th><input type="text" name="dots_filter_pos_name" placeholder="Название" class="input_small"></th>' +
                '<th><input type="text" name="dots_filter_owner" placeholder="Владелец"></th>' +
                '<th style="width:300px;"><input type="text" name="dots_filter_address" placeholder="Адрес">'+
                    '<button class="btn btn_small btn_icon fr" onclick="filterDots<?=$postfix?>($(this))"><i class="icon-find"></i></button>'+
                '</th>' +
                '<th class="td_edit"></th>' +
            '</tr>');
            renderCheckbox(block.find('.td_check [type=checkbox]'));
        }

        if(params.ID_EMITENT){
            block.find('[name=dots_filter_id_emi]').val(params.ID_EMITENT);
        }
        if(params.ID_TO){
            block.find('[name=dots_filter_id_to]').val(params.ID_TO);
        }
        if(params.OWNER){
            block.find('[name=dots_filter_owner]').val(params.OWNER);
        }
        if(params.POS_ADDRESS){
            block.find('[name=dots_filter_address]').val(params.POS_ADDRESS);
        }
        if(params.POS_NAME){
            block.find('[name=dots_filter_pos_name]').val(params.POS_NAME);
        }
        if(params.PROJECT_NAME){
            block.find('[name=dots_filter_project_name]').val(params.PROJECT_NAME);
        }
    }

    function renderAjaxPaginationDotsListError<?=$postfix?>(block, params)
    {
        renderFilterDotsList<?=$postfix?>(block, params);

        var subBlock = block.find('tbody');

        var tpl = $('<tr>' +
            '<td colspan="8" class="center"><i>Данные отсутствуют</i></td>' +
        '</tr>');

        subBlock.append(tpl);
    }

    function renderAjaxPaginationDotsList<?=$postfix?>(data, block, params)
    {
        renderFilterDotsList<?=$postfix?>(block, params);

        var subBlock = block.find('tbody');

        for(var i in data){
            var tpl = $('<tr>' +
                '<td class="td_check" />' +
                '<td class="dot_td_project_name" />' +
                '<td class="dot_td_id_emi" />' +
                '<td class="dot_td_id_to" />' +
                '<td class="dot_td_pos_name"/>' +
                '<td class="dot_td_owner"/>' +
                '<td class="dot_td_address"/>' +
                '<td class="td_edit"/>' +
            '</tr>');

            tpl.attr('POS_ID', data[i].POS_ID);
            tpl.find('.td_check').html('<input type="checkbox" name="pos_id" value="'+ data[i].POS_ID +'">');
            tpl.find('.dot_td_project_name').text(data[i].PROJECT_NAME);
            tpl.find('.dot_td_id_emi').text(data[i].ID_EMITENT);
            tpl.find('.dot_td_id_to').text(data[i].ID_TO);
            tpl.find('.dot_td_pos_name').text(data[i].POS_NAME);
            tpl.find('.dot_td_owner').text(data[i].OWNER);
            tpl.find('.dot_td_address').text(data[i].POS_ADDRESS);
            tpl.find('.td_edit').html('<span class="btn btn_green btn_small btn_icon"><i class="icon-pen"></span>');

            subBlock.append(tpl);
            renderCheckbox(tpl.find('.td_check [type=checkbox]'));
        }

        if($('.tabs_dots_groups .action_del', block).is(':visible')){
            $('.td_edit', block).show();
        }
        if($('.tabs_dots_groups .action_del', block).is(':visible') || $('[name=show_dots_<?=$postfix?>]').length){
            $('.td_check', block).show();
        }
    }
    
    function filterDots<?=$postfix?>(btn)
    {
        var block = btn.closest('.ajax_block_dots_list_<?=$postfix?>_out');

        var params = {
            PROJECT_NAME:   $('[name=dots_filter_project_name]', block).val(),
            ID_EMITENT:     $('[name=dots_filter_id_emi]', block).val(),
            ID_TO:          $('[name=dots_filter_id_to]', block).val(),
            POS_NAME:       $('[name=dots_filter_pos_name]', block).val(),
            OWNER:          $('[name=dots_filter_owner]', block).val(),
            POS_ADDRESS:    $('[name=dots_filter_address]', block).val(),
            show_all_btn:   true,
            onError:        renderAjaxPaginationDotsListError<?=$postfix?>
        };

        if($('[name=group_id_<?=$postfix?>]').length){
            params.group_id = $('[name=group_id_<?=$postfix?>]').val();
        }

        block.empty().addClass('block_loading');

        paginationAjax('/control/load-dots/', 'ajax_block_dots_list_<?=$postfix?>', renderAjaxPaginationDotsList<?=$postfix?>, params);
    }
</script>