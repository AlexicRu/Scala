<input type="hidden" name="card_id" value="<?=$card['CARD_ID']?>">

<?if(Access::allow('clients_card_edit')){?>
    <div class="fr">
        <?if($card['CARD_STATE'] == Model_Card::CARD_STATE_BLOCKED){?>
            <button class="btn btn_green btn_card_toggle"><span style="display: none">Заблокировать</span><span>Разблокировать</span></button>
        <?}else{?>
            <button class="btn btn_red btn_card_toggle"><span>Заблокировать</span><span style="display: none">Разблокировать</span></button>
        <?}?>
        &nbsp;
        <a href="#card_edit_<?=$card['CARD_ID']?>" class="fancy btn"><i class="icon-pen"></i> Редактировать</a>
    </div>
<?}?>

<b class="f18">Обороты за текущий период:</b><br>
<?=number_format($card['REALIZ_LITRES'], 2, ',', ' ')?> л. / <?=number_format($card['REALIZ_CUR'], 2, ',', ' ')?> <?=Text::RUR?><br><br>

<b class="f18">Последняя заправка:</b>
<div class="line_inner">
    <span class="gray"><?=$lastFilling['LAST_SERV_DATE']?></span> &nbsp;&nbsp;&nbsp; <b><?=$lastFilling['LAST_SERV_POS']?></b> <div class="fr"><?=$lastFilling['LAST_SERV_SERVICE']?> <?=number_format($lastFilling['LAST_SERV_AMOUNT'], 2, ',', ' ')?> л. / <?=number_format($lastFilling['LAST_SERV_CUR'], 2, ',', ' ')?> <?=Text::RUR?></div>
</div>
<br>

<b class="f18">Ограничения по топливу:</b>
<?if(!empty($oilRestrictions)){?>
    <table>
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

<?if(!empty($operationsHistory)){?>
    <b class="f18">История операций:</b>
    <?foreach($operationsHistory as $operation){?>
        <div class="line_inner">
            <span class="gray"><?=$operation['H_DATE']?></span> &nbsp;&nbsp;&nbsp; <?=$operation['M_FIO']?> <div class="fr"><?=$operation['SHORT_DESCRIPTION']?></div>
        </div>
    <?}?>
<?}?>

<?if(Access::allow('clients_card_edit')){?>
    <?=$popupCardEdit?>
<?}?>