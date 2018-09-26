<div class="padding__20">
    <i class="gray">Отображение платежей только по типу источника "Цепочка договоров внутри системы"</i>
    <br>
    <div class="ajax_block_payments_history_out"></div>
</div>

<script>
    $(function(){
        paginationAjax('/suppliers/contract-payments-history/' + $('[name=suppliers_contracts_list]').val(), 'ajax_block_payments_history', renderAjaxPaginationPaymentsHistory);
    });
    function renderAjaxPaginationPaymentsHistory(data, block)
    {
        for(var i = 0 in data){
            var tpl = $('<div class="line_inner">'+
                '<span class="gray" /> &nbsp;&nbsp;&nbsp; '+
                '<b class="line_inner_150" />'+
                '<span class="gray" /> &nbsp;&nbsp;&nbsp; '+
                '<b>' +
                '</div>');

            tpl.find('span.gray:first').text(data[i].ORDER_DATE);
            tpl.find('b.line_inner_150').text('№' + data[i].ORDER_NUM);
            tpl.find('span.gray:last').text('Сумма');
            tpl.find('b:last').html(number_format(data[i].SUMPAY, 2, ',', ' ') + ' <?=Text::RUR?>');

            var tplAdditional = $('<div class="line_inner__additional" />');

            if (data[i].DATE_IN) {
                tplAdditional.append('<div class="date_in_comment gray"><i>Внесена:</i> '+ data[i].DATE_IN +'</div>');
            }

            if (data[i].PAY_COMMENT) {
                tplAdditional.append('<div class="full_comment"><i>Комментарий:</i> '+ data[i].PAY_COMMENT +'</div>');
            }

            tpl.append(tplAdditional);

            block.append(tpl);
        }
    }
</script>
