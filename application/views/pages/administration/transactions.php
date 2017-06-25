<h1>Транзакции</h1>

<div class="tabs_block tabs_switcher tabs_administration_transactions">
    <div class="tabs">
        <span tab="errors" class="tab active">Отказные</span><span tab="history" class="tab">История загрузок</span>
    </div>
    <div class="tabs_content">
        <div tab_content="errors" class="tab_content active">
            <div class="ajax_block_administration_transactions_errors_out block_loading"></div>
        </div>
        <div tab_content="history" class="tab_content">
            <div class="ajax_block_administration_transactions_history_out block_loading"></div>
        </div>
    </div>
</div>

<script>
    var skipColumns = ['RNUM', 'TRN_CURRENCY', 'AGENT_ID', 'DATETIME_PROCESS'];

    $(function(){
        var params = {
            show_all_btn:true
        };

        paginationAjax('/administration/transactions_errors', 'ajax_block_administration_transactions_errors', renderAjaxPaginationAdminTransactions, params);
        paginationAjax('/administration/transactions_history', 'ajax_block_administration_transactions_history', renderAjaxPaginationAdminTransactions);
    });

    function renderAjaxPaginationAdminTransactions(data, block)
    {
        var i, j, tr, table;

        if(block.find('table').length == 0) {
            var scrollBlock = $('<div class="scroll_horizontal"><table class="table table_small" /></div>');
            block.append(scrollBlock);

            table = block.find('table');

            //draw headers
            for(i in data){
                tr = $('<tr />');

                for(j in data[i]){
                    if(skipColumns.indexOf(j) != -1){
                        continue;
                    }
                    tr.append('<th>'+ j +'</th>');
                }

                table.append(tr);
                break;
            }
        }

        table = block.find('tbody');

        //draw body
        for(i in data){
            tr = $('<tr />');

            for(j in data[i]){
                if(skipColumns.indexOf(j) != -1){
                    continue;
                }
                tr.append('<td>'+ data[i][j] +'</td>');
            }

            table.append(tr);
        }
    }
</script>