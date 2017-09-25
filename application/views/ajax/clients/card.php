<input type="hidden" name="card_id" value="<?=$card['CARD_ID']?>">

<?
$user = Auth::instance()->get_user();
?>

<div class="fr">
    <?if(in_array($card['BLOCK_AVAILABLE'], [1,2]) || Access::allow('clients_card_toggle')){?>
        <?if($card['CARD_STATE'] == Model_Card::CARD_STATE_BLOCKED){?>
            <button class="btn btn_green btn_card_toggle" block_available="<?=$card['BLOCK_AVAILABLE']?>"><span style="display: none"><i class="icon-block"></i> Заблокировать</span><span><i class="icon-backblock"></i> Разблокировать</span></button>
        <?}else{?>
            <button class="btn btn_red btn_card_toggle" block_available="<?=$card['BLOCK_AVAILABLE']?>"><span><i class="icon-block"></i> Заблокировать</span><span style="display: none"><i class="icon-backblock"></i> Разблокировать</span></button>
        <?}?>
    <?}?>
    <?if(Access::allow('clients_card_withdraw')){?>
        <a href="#" class="btn btn_orange" onclick="cardWithdraw('<?=$card['CARD_ID']?>', <?=$card['BLOCK_AVAILABLE']?>)"><i class="icon-cancel"></i> Изъять</a>
    <?}?>
    <?if(Access::allow('clients_card_edit')){?>
        <a href="#card_edit_holder_<?=$card['CARD_ID']?>" class="fancy btn"><i class="icon-pen"></i> Редактировать</a>
    <?}?>
</div>

<b class="f18">Обороты за текущий период:</b><br>
<?=number_format($card['REALIZ_LITRES'], 2, ',', ' ')?> л. / <?=number_format($card['REALIZ_CUR'], 2, ',', ' ')?> <?=Text::RUR?><br><br>

<b class="f18">Последняя заправка:</b>
<div class="line_inner">
    <span class="gray"><?=$lastFilling['LAST_SERV_DATE']?></span> &nbsp;&nbsp;&nbsp; <b><?=$lastFilling['LAST_SERV_POS']?></b> <div class="fr"><?=$lastFilling['LAST_SERV_SERVICE']?> <?=number_format($lastFilling['LAST_SERV_AMOUNT'], 2, ',', ' ')?> л. / <?=number_format($lastFilling['LAST_SERV_CUR'], 2, ',', ' ')?> <?=Text::RUR?></div>
</div>
<br>

<div class="fr">
    <?if(Access::allow('clients_card_edit')){?>
        <?if(!empty($card['CHANGE_LIMIT_AVAILABLE']) && Access::allow('clients_card_edit_limits')){?>
            <a href="#card_edit_limits_<?=$card['CARD_ID']?>" class="fancy btn btn_icon"><i class="icon-pen"></i></a>
        <?}?>
    <?}?>
</div>
<b class="f18">Ограничения по топливу:</b>
<?if(!empty($oilRestrictions)){?>
    <table class="tbl_spacing">
        <?foreach($oilRestrictions as $restrictions){
            $restrict = reset($restrictions);
            ?>
            <tr>
                <td class="gray right">
                    <?foreach($restrictions as $restriction){?>
                        <?=$restriction['DESCRIPTION']?>:<br>
                    <?}?>
                </td>
                <td class="line_inner">
                    <?=$restrict['LIMIT_VALUE']?>
                    <?=Model_Card::$cardLimitsParams[$restrict['LIMIT_PARAM']]?>
                    <?=Model_Card::$cardLimitsTypes[$restrict['LIMIT_TYPE']]?>
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
        paginationAjax('/clients/card_operations_history/<?=$card['CARD_ID']?>?contract_id=' + $('[name=contracts_list]').val(), 'ajax_block_operations_history_<?=$card['CARD_ID']?>', renderAjaxPaginationOperationsHistory);
    });
</script>
