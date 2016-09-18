<div class="tc_top_line">
    <span class="gray">Баланс по договору:</span> <?=number_format($balance['BALANCE'], 2, ',', ' ')?> <?=Text::RUR?>
    <div class="fr">
        <?if(Access::allow('clients_bill_add')){?>
            <a href="#contract_bill_add" class="fancy btn">Выставить счет</a>
        <?}?>
        <?if(Access::allow('clients_bill_print')){?>
            <a href="#contract_bill_print" class="fancy btn">Печать счетов</a>
        <?}?>
    </div>
</div>
<div class="as_table">
    <div class="col line_inner">
        <?if(Access::allow('view_contract_balances')){?>
        <b class="f18">Остатки по договору:</b>
        <br>Без ограничений<br>
        <?/*<table>
            <tbody><tr>
                <td class="gray right" width="160">ДТ:<br>ДТ зимнее:<br>ДТ Евро:</td>
                <td class="f24 white_block">
                    985 л.
                </td>
            </tr>
            <tr>
                <td class="gray right">Бензин АИ-95:</td>
                <td class="f24 white_block">
                    150 л.
                </td>
            </tr>
            </tbody></table>*/?>
        <br>
        <?}?>

        <b class="f18">Обороты по договору:</b>
        <div class="white_block">
            <div class="gray">за текущий период:</div>
            <div class="f24"><?=number_format($turnover['MONTH_REALIZ'], 2, ',', ' ')?> л. / <?=number_format($turnover['MONTH_REALIZ_CUR'], 2, ',', ' ')?> <?=Text::RUR?></div>
        </div>

        <div class="as_white">
            <div class="gray">за прошлый период:</div>
            <?=number_format($turnover['LAST_MONTH_REALIZ'], 2, ',', ' ')?> л. / <?=number_format($turnover['LAST_MONTH_REALIZ_CUR'], 2, ',', ' ')?> <?=Text::RUR?>
        </div>
    </div><div class="col">
        <?if(Access::allow('clients_payment_add')){?>
            <div class="fr">
                <a href="#contract_payment_add" class="fancy btn">+ Добавить платеж</a>
            </div>
        <?}?>


        <div class="ajax_block_payments_history_out">
            <b class="f18">Платежи:</b><br><br>
        </div>
        <script>
            $(function(){
                paginationAjax('/clients/account_payments_history/' + $('[name=contracts_list]').val(), 'ajax_block_payments_history', renderAjaxPaginationPaymentsHistory);
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
                    <?if(Access::allow('clients_payment_del')){?>
                        tpl.append('<div class="fr"><a href="#" class="red del link_del_contract_payment">Удалить <i class="icon-cancel"></i></a></div>');
                    <?}?>

                    tpl.attr('guid', data[i].ORDER_GUID);
                    tpl.find('span.gray:first').text(data[i].ORDER_DATE);
                    tpl.find('b.line_inner_150').text('№' + data[i].ORDER_NUM);
                    tpl.find('span.gray:last').text('Сумма');
                    tpl.find('b:last').html(number_format(data[i].SUMPAY, 2, ',', ' ') + ' <?=Text::RUR?>');
                    block.append(tpl);
                }
            }
        </script>
    </div>
</div>

<?if(Access::allow('clients_payment_add')){?>
    <?=$popupContractPaymentAdd?>
<?}?>
<?if(Access::allow('clients_bill_add')){?>
    <?=$popupContractBillAdd?>
<?}?>
<?if(Access::allow('clients_bill_print')){?>
    <?=$popupContractBillPrint?>
<?}?>

<script>
    $(function(){
        <?if(Access::allow('clients_payment_del')){?>
            $(document).off('click', '.link_del_contract_payment').on('click', '.link_del_contract_payment', function(){
                var t = $(this);
                var row = t.closest('[guid]');

                if(!confirm('Удалить платеж ' + row.find('b.line_inner_150').text())){
                    return false;
                }

                var params = {
                    contract_id:    $('[name=contracts_list]').val(),
                    guid:           row.attr('guid')
                };

                $.post('/clients/contract_payment_delete', {params:params}, function(data){
                    if(data.success){
                        message(1, 'Платеж успешно удален');
                        loadContract('account');
                    }else{
                        message(0, 'Ошибка удаления платежа');
                    }
                });

                return false;
            });
        <?}?>
    });
</script>
