<div class="ajax_block_dots_<?=$groupId?>_out">

</div>

<script>
    $(function(){
        paginationAjax('/control/load_group_dots/', 'ajax_block_dots_<?=$groupId?>', renderAjaxPaginationDots<?=$groupId?>, {group_id: <?=$groupId?>});
    });

    function renderAjaxPaginationDots<?=$groupId?>(data, block)
    {
        if(block.find('> table').size() == 0){
            block.append('<table class="table table_small table_fullscreen"></table>');
            block = block.find('table');

            block.append('<tr>' +
                '<th>ID</th>' +
                '<th><nobr>ID EMI</nobr></th>' +
                '<th><nobr>ID TO</nobr></th>' +
                '<th><nobr>POS name</nobr></th>' +
                '<th>Владелец</th>' +
                '<th>Адрес</th>' +
            '</tr>');
        }else{
            block = block.find('table');
        }

        for(var i in data){
            var tpl = $('<tr>' +
                '<td class="dot_td_id" />' +
                '<td class="dot_td_id_emi" />' +
                '<td class="dot_td_id_to" />' +
                '<td class="dot_td_pos_name"/>' +
                '<td class="dot_td_owner"/>' +
                '<td class="dot_td_address"/>' +
            '</tr>');

            tpl.find('.dot_td_id').text(data[i].POS_ID);
            tpl.find('.dot_td_id_emi').text(data[i].ID_EMITENT);
            tpl.find('.dot_td_id_to').text(data[i].ID_TO);
            tpl.find('.dot_td_pos_name').text(data[i].POS_NAME);
            tpl.find('.dot_td_owner').text(data[i].OWNER);
            tpl.find('.dot_td_address').text(data[i].POS_ADDRESS);

            block.append(tpl);
        }

        renderScroll($('.tabs_group_dots .scroll'));
    }
</script>