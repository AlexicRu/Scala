<div class="webtour-account">
<div class="tc_top_line">
    <span class="gray">Баланс по договору:</span> <?=number_format($balance['BALANCE'], 2, ',', ' ')?> <?=Text::RUR?>
    <div class="fr">
        <?if(Access::allow('clients_bill-add')){?>
            <a href="#contract_bill_add" class="fancy btn">Выставить счет</a>
        <?}?>
        <?if(Access::allow('clients_bill-print')){?>
            <a href="#contract_bill_print" class="fancy btn">Печать счетов</a>
        <?}?>
    </div>
</div>
<div class="as_table">
    <div class="col line_inner">
        <?if(Access::allow('view_contract_balances')){?>
            <?if(Access::allow('clients-contract-limits-edit')){?>
                <div class="fr"><a href="#contract_limits_edit" class="fancy btn btn_green btn_icon btn_small"><i class="icon-pen"></i></a></div>
            <?}?>

            <b class="f18">Остатки по договору:</b>

            <?if (empty($contractLimits)) {?>
                <br>Без ограничений<br>
            <?} else {?>
                <table class="tbl_spacing">
                    <?foreach($contractLimits as $restrictions){
                        $restrict = reset($restrictions);
                        ?>
                        <tr>
                            <td class="gray right">
                                <?foreach($restrictions as $restriction){?>
                                    <?=$restriction['LONG_DESC']?>:<br>
                                <?}?>
                            </td>
                            <td class="line_inner">
                                <?if($restrict['INFINITELY']){?>
                                    <i>Неограничено</i>
                                <?}else{
                                    $param = Model_Card::$cardLimitsParams[Model_Card::CARD_LIMIT_PARAM_VOLUME];
                                    if ($restrict['CURRENCY'] == Common::CURRENCY_RUR) {
                                        $param = Model_Card::$cardLimitsParams[Model_Card::CARD_LIMIT_PARAM_RUR];
                                    }?>
                                    <b><?=$restrict['REST_LIMIT']?> <?=$param?></b> из <?=$restrict['LIMIT_VALUE']?> <?=$param?>
                                <?}?>
                            </td>
                            <?if(Access::allow('clients_contract_increase_limit')){?>
                            <td>
                                <?if(!$restrict['INFINITELY']){?>
                                    <span class="btn btn_small" onclick="openIncreaseLimitPopup(<?=$restrict['LIMIT_ID']?>)">+</span>
                                <?}?>
                            </td>
                            <?}?>
                        </tr>
                    <?}?>
                </table>
            <?}?>
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
        <?if(Access::allow('clients_payment-add')){?>
            <div class="fr">
                <a href="#contract_payment_add" class="fancy btn">+ Добавить платеж</a>
            </div>
        <?}?>


        <div class="ajax_block_payments_history_out">
            <b class="f18">Платежи:</b><br><br>
        </div>
        <script>
            $(function(){
                paginationAjax('/clients/account-payments-history/' + $('[name=contracts_list]').val(), 'ajax_block_payments_history', renderAjaxPaginationPaymentsHistory);
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
                    <?if(Access::allow('clients_payment-del')){?>
                        tpl.prepend('<div class="fr"><a href="#" class="red del link_del_contract_payment">Удалить <i class="icon-cancel"></i></a></div>');
                    <?}?>

                    tpl.attr('guid', data[i].ORDER_GUID);
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

                renderTootip();
            }
        </script>
    </div>
</div>
</div>

<?if(Access::allow('clients_payment-add')){?>
    <?=$popupContractPaymentAdd?>
<?}?>
<?if(Access::allow('clients_bill-add')){?>
    <?=$popupContractBillAdd?>
<?}?>
<?if(Access::allow('clients_bill-print')){?>
    <?=$popupContractBillPrint?>
<?}?>
<?if(Access::allow('view_contract_balances') && Access::allow('clients_contract-limits-edit')){?>
    <?=$popupContractLimitsEdit?>
<?}?>
<?if(Access::allow('clients_contract-increase-limit')){?>
    <?=$popupContractLimitIncrease?>
<?}?>

<script>
    $(function(){
        <?if(Access::allow('clients_payment-del')){?>
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

                $.post('/clients/contract-payment-delete', {params:params}, function(data){
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

    <?if(Access::allow('clients_contract_increase_limit')){?>
    var increaseLimitId = 0;
    function openIncreaseLimitPopup(limitId)
    {
        increaseLimitId = limitId;

        $.fancybox('#contract_increase_limit', {
            padding: [0,0,0,0]
        });
    }
    <?}?>
</script>
