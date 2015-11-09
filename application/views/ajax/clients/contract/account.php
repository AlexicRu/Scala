<div class="tc_top_line">
    <span class="gray">Баланс по договору:</span> <?=number_format($balance['BALANCE'], 2, ',', ' ')?>
</div>
<div class="as_table">
    <div class="col line_inner">
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
        <b class="f18">Платежи:</b><br><br>
        <?
        if(!empty($paymentsHistory)) {
            foreach ($paymentsHistory as $history) {
                ?>
                <div class="line_inner" guid="<?=$history['ORDER_GUID']?>">
                    <span class="gray"><?=$history['ORDER_DATE']?></span> &nbsp;&nbsp;&nbsp;
                    <b class="line_inner_150">№<?=$history['ORDER_NUM']?></b>
                    <span class="gray">Сумма</span> &nbsp;&nbsp;&nbsp;
                    <b><?=number_format($history['SUMPAY'], 2, ',', ' ')?> <?=Text::RUR?></b>

                    <?if(Access::allow('clients_payment_del')){?>
                        <div class="fr"><a href="#" class="red del link_del_contract_payment">Удалить <i class="icon-cancel"></i></a></div>
                    <?}?>
                </div>
            <?
            }
        }else{
            echo '<div>Платежи отсутствуют</div>';
        }?>
    </div>
</div>

<?if(Access::allow('clients_payment_add')){?>
    <?=$popupContractPaymentAdd?>
<?}?>

<script>
    $(function(){
        <?if(Access::allow('clients_payment_del')){?>
            $('.link_del_contract_payment').on('click', function(){
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
