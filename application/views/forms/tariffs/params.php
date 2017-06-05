<table class="params_block" uid="<?=$uid?>">
    <tr>
        <td class="gray right" width="100">Тип:</td>
        <td>
            <select name="DISC_TYPE">
                <?foreach(Model_Tariff::$paramsTypes as $paramsTypeId => $paramsType){?>
                    <option value="<?=$paramsTypeId?>"><?=$paramsType?></option>
                <?}?>
            </select>
        </td>
        <td class="gray right" width="100">Параметр:</td>
        <td>
            <?foreach(Model_Tariff::$paramsTypesParams as $paramsTypeId => $paramsParams){?>
                <select name="DISC_PARAM" class="disc_param_select" disc_type="<?=$paramsTypeId?>">
                    <?foreach($paramsParams as $paramsParamsId => $paramsParam){?>
                        <option value="<?=($paramsParamsId+1)?>">
                            <?=Model_Tariff::$paramsParams[$paramsParam]?>
                        </option>
                    <?}?>
                </select>
            <?}?>
        </td>
        <td class="gray right" width="100">Значение:</td>
        <td>
            <input type="number" name="DISC_VALUE" value="<?=(isset($params['DISC_VALUE']) ? $params['DISC_VALUE'] : '')?>">
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <label><input type="checkbox" name="CLOSE_CALCULATION" <?=(!isset($params['CLOSE_CALCULATION']) || !empty($params['CLOSE_CALCULATION']) ? 'checked' : '')?>> Завершить расчет</label>
        </td>
    </tr>
</table>

<script>
    $(function () {
        $('[name=CLOSE_CALCULATION]').each(function () {
            renderCheckbox($(this));
        });

        $('.params_block[uid=<?=$uid?>] [name=DISC_TYPE]').on('change', function () {
            onChangeParam($(this));
        });

        <?if(!empty($params)){?>
            changeParam('<?=$uid?>', <?=$params['DISC_TYPE']?>, <?=$params['DISC_PARAM']?>);
        <?}else{?>
            changeParam('<?=$uid?>');
        <?}?>
    });
</script>