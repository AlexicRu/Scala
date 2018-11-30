<div class="ajax_block_client_bills_list_out">

</div>

<script>
    $(function(){
        paginationAjax('/clients/bills-list', 'ajax_block_client_bills_list', renderAjaxPaginationClientBillsList, {
            'contract_id': $('[name=contracts_list]').val()
        });
    });

    function renderAjaxPaginationClientBillsList(data, block) {
        for(var i = 0 in data){
            var tpl = $('<div class="line_inner"><span class="gray"></span> &nbsp;&nbsp;&nbsp;</div>');
            tpl.find('span').text(data[i].DATE_WEB);
            tpl.append('<span class="line_inner_200">' + data[i].CONTRACT_NAME + '</span>');
            tpl.append('<b class="line_inner_100">' + data[i].NUM_REPORT + '</b>');
            tpl.append('<nobr class="line_inner_100">' + number_format(data[i].INVOICE_SUM, 2, ',', ' ') + ' <?=Text::RUR?></nobr>');
            tpl.append('<a href="/reports/generate?type=<?=Model_Report::REPORT_TYPE_BILL?>&format=pdf&contract_id=' + data[i].CONTRACT_ID + '&invoice_number=' + data[i].INVOICE_NUMBER + '" class="btn btn_small" target="_blank"><i class="icon-download"></i> Скачать</a>');

            <?if(Access::allow('download_bill_as_xls')){?>
            tpl.append('&nbsp;<a href="/reports/generate?type=<?=Model_Report::REPORT_TYPE_BILL?>&format=xls&contract_id=' + data[i].CONTRACT_ID + '&invoice_number=' + data[i].INVOICE_NUMBER + '" class="btn btn_small" target="_blank">В Excel</a>');
            <?}?>

            block.append(tpl);
        }
    }

</script>