<tr limit_group="<?=(!empty($limit['LIMIT_ID']) ? $limit['LIMIT_ID'] : -1)?>">
    <td>
        <?
        if (!empty($limit['services'])) {
            foreach ($limit['services'] as $limitService) {
                echo Form::buildLimitService($cardId, $limitService, $postfix);
            }
        }
        ?>
        <div>
            <nobr>
                <?if ($settings['canAddService']) {?>
                    <button class="btn btn_small btn_green btn_card_edit_add_serviсe" onclick="cardEditAddService_<?=$postfix?>($(this))">+ добавить услугу</button>
                <?}?>
                <?if ($settings['canDelLimit']) {?>
                    <button class="btn btn_small btn_red btn_card_edit_del_limit" onclick="cardEditDelLimit_<?=$postfix?>($(this))">&times; удалить лимит</button>
                <?}?>
            </nobr>
        </div>
    </td>
    <td class="v_top">
        <input type="number" name="limit_value" value="<?=(isset($limit['LIMIT_VALUE']) ? $limit['LIMIT_VALUE'] : '')?>" placeholder="Объем / сумма" class="input_mini"
            <?if (!$settings['canUseFloat']) {?>onkeypress="$(this).val(parseInt($(this).val()))"<?}?>
            min="<?=$settings['minValue']?>"
        >
    </td>
    <td class="v_top">
        <select name="unit_type" <?=(empty($settings['editSelect']) ? 'disabled' : '')?>>
            <?foreach($settings['limitParams'] as $limitParam => $value){?>
                <option value="<?=$limitParam?>" <?if(isset($limit['UNIT_TYPE']) && $limitParam == $limit['UNIT_TYPE']){?>selected<?}?>><?=$value?></option>
            <?}?>
        </select>
    </td>
    <?if ($settings['cntTypes']) {?>
        <td class="v_top">
            <input type="text" name="duration_value" value="<?=(isset($limit['DURATION_VALUE']) ? $limit['DURATION_VALUE'] : '')?>" placeholder="Кол-во" class="input_mini" <?=(empty($settings['editDurationValue']) ? 'disabled' : '')?>>
        </td>
    <?}?>
    <td class="v_top">
        <select name="duration_type" <?=(empty($settings['editSelect']) ? 'disabled' : '')?>>
            <?foreach($settings['limitTypes'] as $limitType => $value){?>
                <option value="<?=$limitType?>" <?if(isset($limit['DURATION_TYPE']) && $limitType == $limit['DURATION_TYPE']){?>selected<?}?>><?=$value?></option>
            <?}?>
        </select>
    </td>
</tr>