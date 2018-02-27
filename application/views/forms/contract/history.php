<div class="ajax_block_contract_history_out">

</div>

<script>
    $(function(){
        paginationAjax('/clients/contract-history', 'ajax_block_contract_history', renderAjaxPaginationContractHistory, {
            'contract_id': $('[name=contracts_list]').val()
        });
    });

    function renderAjaxPaginationContractHistory(data, block) {
        for(var i = 0 in data){
            var tpl = $('<div class="line_inner"><span class="gray"></span> &nbsp;&nbsp;&nbsp;</div>');
            tpl.find('span').text(data[i].DATE_TIME);
            tpl.append(data[i].CONTRACT_EVENT);
            block.append(tpl);
        }
    }
</script>