<input type="hidden" name="card_id" value="<?=$card['CARD_ID']?>">

<div class="tbl">
    <div>
        <span class="f20">Карта: <b><?=$card['CARD_ID']?></b></span>
    </div>
    <div class="right">
        <?if(Access::allow('view_card_info')){?>
            <span class="btn" toggle="card_info_block">Инфо</span>
        <?}?>
        <?if(Access::allow('clients_card_toggle_full')){?>
            <?if(in_array($card['BLOCK_AVAILABLE'], [1,2]) || Access::allow('clients_card-toggle')){?>
                <?if($card['CARD_STATE'] == Model_Card::CARD_STATE_BLOCKED){?>
                    <button class="btn btn_green btn_card_toggle" block_available="<?=$card['BLOCK_AVAILABLE']?>"><span style="display: none"><i class="icon-block"></i> Заблокировать</span><span><i class="icon-backblock"></i> Разблокировать</span></button>
                <?}else{?>
                    <button class="btn btn_red btn_card_toggle" block_available="<?=$card['BLOCK_AVAILABLE']?>"><span><i class="icon-block"></i> Заблокировать</span><span style="display: none"><i class="icon-backblock"></i> Разблокировать</span></button>
                <?}?>
            <?}?>
        <?}?>
        <?if(Access::allow('clients_card-withdraw')){?>
            <a href="#" class="btn btn_orange" onclick="cardWithdraw('<?=$card['CARD_ID']?>', <?=$card['BLOCK_AVAILABLE']?>)"><i class="icon-cancel"></i> Изъять</a>
        <?}?>
        <?if(Access::allow('clients_card_edit')){?>
            <a href="#card_edit_holder_<?=$card['CARD_ID']?>" class="fancy btn btn_icon"><i class="icon-pen"></i></a>
        <?}?>
    </div>
</div>

<hr class="out">

<?if(Access::allow('view_card_info')){?>
    <div class="dn" toggle_block="card_info_block">
        <table>
            <tr>
                <td class="gray right" width="400">Тип источника карты</td>
                <td><?=$cardInfo['CARD_FROM']?></td>
            </tr>
            <tr>
                <td class="gray right">Источник карты</td>
                <td><?=$cardInfo['SOURCE_NAME']?></td>
            </tr>
            <tr>
                <td class="gray right">Дата последнего изменения лимита</td>
                <td><?=$cardInfo['RECORD_LIMIT_DATE']?></td>
            </tr>
            <tr>
                <td class="gray right">Статус применения лимита в источнике</td>
                <td><?=$cardInfo['LIMIT_SOURCE_STATUS']?></td>
            </tr>
            <tr>
                <td class="gray right">Дата последнего изменения состояния</td>
                <td><?=$cardInfo['RECORD_STATE_DATE']?></td>
            </tr>
            <tr>
                <td class="gray right">Статус применения состояния в источнике</td>
                <td><?=$cardInfo['STATE_SOURCE_STATUS']?></td>
            </tr>
        </table>

        <hr class="out">
    </div>
<?}?>

<b class="f18">Обороты за текущий период:</b><br>
<?=number_format($card['REALIZ_LITRES'], 2, ',', ' ')?> л. / <?=number_format($card['REALIZ_CUR'], 2, ',', ' ')?> <?=Text::RUR?><br><br>

<?if (!empty($transactions)) {?>
    <?if (count($transactions) > 1) {?>
    <span class="fr btn btn_small btn_reverse" toggle="last_transactions">
        <span toggle_block="last_transactions">+</span>
        <span toggle_block="last_transactions" style="display: none">-</span>
    </span>
    <?}?>

    <b class="f18">Последние заправки:</b>

    <?foreach ($transactions as $index => $transaction) {?>
        <div class="line_inner" <?=($index ? 'toggle_block="last_transactions" style="display:none"' : '')?>>
            <span class="gray"><?=$transaction['DATE_TRN_FORMATTED']?> <?=$transaction['TIME_TRN']?></span> &nbsp;&nbsp;&nbsp;
            <b><?=$transaction['POS_PETROL_NAME']?></b>
            <div class="fr">
                <b><?=$transaction['LONG_DESC']?></b> - <?=number_format($transaction['SERVICE_AMOUNT'], 2, ',', ' ')?> л. / <?=number_format($transaction['SUMPRICE_DISCOUNT'], 2, ',', ' ')?> <?=Text::RUR?>
            </div>
            <br>
            <span style="visibility: hidden"><?=$transaction['DATE_TRN_FORMATTED']?> <?=$transaction['TIME_TRN']?></span> &nbsp;&nbsp;&nbsp;
            <?=$transaction['POS_ADDRESS']?>
        </div>
    <?}?>
<?}?>

<br>

<div class="fr">
    <?if(Access::allow('clients_card_edit')){?>
        <?if(!empty($card['CHANGE_LIMIT_AVAILABLE']) && Access::allow('clients_card-edit-limits')){?>
            <a href="#card_edit_limits_<?=$card['CARD_ID']?>" class="fancy btn btn_icon"><i class="icon-pen"></i></a>
        <?}?>
    <?}?>
</div>

<b class="f18">Ограничения по топливу:</b>
<?if(!empty($oilRestrictions)){
    $systemId = $card['SYSTEM_ID'];

    switch ($systemId){
        case Model_Card::CARD_SYSTEM_GPN:
            $limitTypes = Model_Card::$cardLimitsTypesFull;
            break;
        default:
            $limitTypes = Model_Card::$cardLimitsTypes;
    }

    ?>
    <table class="tbl_spacing">
        <tr>
            <td></td>
            <td>Лимит</td>
            <td>Остаток</td>
        </tr>
        <?foreach($oilRestrictions as $restriction){?>
            <tr>
                <td class="gray right">
                    <?foreach($restriction['services'] as $service){?>
                        <?=$service['name']?>:<br>
                    <?}?>
                </td>
                <td class="line_inner">
                    <?if ($systemId == 5) {?>
                        <?=$restriction['LIMIT_VALUE']?>
                        <?=Model_Card::$cardLimitsParams[$restriction['UNIT_TYPE']]?>,
                        <?=$limitTypes[$restriction['DURATION_TYPE']]?>: <?=$restriction['DURATION_VALUE']?>
                    <?}else{?>
                        <?=$restriction['LIMIT_VALUE']?>
                        <?=Model_Card::$cardLimitsParams[$restriction['UNIT_TYPE']]?>
                        <?=$limitTypes[$restriction['DURATION_TYPE']]?>
                    <?}?>
                </td>
                <td class="line_inner">
                    <?=$restriction['REST_LIMIT']?>
                    <?=Model_Card::$cardLimitsParams[$restriction['UNIT_TYPE']]?>
                </td>
            </tr>
        <?}?>
    </table>
<?}else{?><div class="gray">Не указаны</div><?}?>
<br>

<div class="ajax_block_operations_history_<?=$card['CARD_ID']?>_out">
    <b class="f18">История операций:</b>
</div>

<?if(Access::allow('clients_card_edit')){?>
    <?=$popupCardHolderEdit?>
    <?=$popupCardLimitsEdit?>
<?}?>

<script>
    $(function(){
        paginationAjax('/clients/card-operations-history/<?=$card['CARD_ID']?>?contract_id=' + $('[name=contracts_list]').val(), 'ajax_block_operations_history_<?=$card['CARD_ID']?>', renderAjaxPaginationOperationsHistory);
    });
</script>
