<table class="table_form form_contract_limits_edit">
    <tr>
        <td class="gray right v_top" width="170">Ограничения по топливу:</td>
        <td>
            <table class="table_form table_form_limits">
                <?foreach($contractLimits as $limits){
                    $limitFirst = reset($limits);
                    ?>
                    <tr limit_group>
                        <td>
                            <?foreach($limits as $limit){?>
                                <div class="form_elem" limit_service><nobr>
                                        <select name="limit_service" onchange="checkServices()">
                                            <?foreach($servicesList as $service){?>
                                                <option value="<?=$service['SERVICE_ID']?>" <?if($service['SERVICE_ID'] == $limit['SERVICE_ID']){?>selected<?}?>><?=$service['LONG_DESC']?></option>
                                            <?}?>
                                        </select>
                                        <button class="btn btn_small btn_red btn_contract_limits_edit_del_serviсe" onclick="contractLimitsEditDelService($(this))">&times;</button>
                                    </nobr></div>
                            <?}?>
                            <div><nobr>
                                    <button class="btn btn_small btn_green btn_contract_limits_edit_add_serviсe" onclick="contractLimitsEditAddService($(this))">+ добавить услугу</button>
                                    <button class="btn btn_small btn_red btn_contract_limits_edit_del_limit" onclick="contractLimitsEditDelLimit($(this))">&times; удалить лимит</button>
                                </nobr></div>
                        </td>
                        <td class="v_top">
                            <input type="text" name="limit_value" value="<?=$limitFirst['LIMIT_VALUE']?>" <?=($limitFirst['INFINITELY'] ? 'disabled' : '')?> placeholder="Объем / сумма">
                        </td>
                        <td class="v_top">
                            <select name="limit_param">
                                <?
                                $param = Model_Card::$cardLimitsParams[Model_Card::CARD_LIMIT_PARAM_VOLUME];
                                if ($limitFirst['CURRENCY'] == Common::CURRENCY_RUR) {
                                    $param = Model_Card::$cardLimitsParams[Model_Card::CARD_LIMIT_PARAM_RUR];
                                }
                                foreach(Model_Card::$cardLimitsParams as $limitParam => $value){?>
                                    <option value="<?=$limitParam?>" <?if($limitParam == $param){?>selected<?}?>><?=$value?></option>
                                <?}?>
                            </select>
                        </td>
                        <td>
                            <label><input name="limit_unlim" type="checkbox" <?=($limitFirst['INFINITELY'] ? 'checked' : '')?> onclick="contractLimitsEditCheckUnlim($(this))"> Без ограничений</label>
                        </td>
                    </tr>
                <?}?>
                <tr>
                    <td><button class="btn btn_green btn_contract_limits_edit_add_limit" onclick="contractLimitsEditAddLimit($(this))">+ Добавить ограничение</button></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <label><input type="checkbox" name="recalc" checked> Пересчет остатков по договору</label>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse btn_contract_limits_edit_go" onclick="contractLimitsEditGo($(this))"><i class="icon-ok"></i> Сохранить</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.form_contract_limits_edit [type=checkbox]').each(function(){
            renderCheckbox($(this));
        });
        checkServices();
    });

    var services_cnt = <?=count($servicesList)?>;

    var services = {
        <?foreach($servicesList as $service){?>
        "<?=$service['SERVICE_ID']?>": "<?=$service['LONG_DESC']?>",
        <?}?>
    };
    var limitParams = {
        <?foreach(Model_Card::$cardLimitsParams as $limitParam => $value){?>
        "<?=$limitParam?>": "<?=$value?>",
        <?}?>
    };

    function contractLimitsEditDelService(t)
    {
        t.closest('[limit_service]').fadeOut();
        setTimeout(function () {
            t.closest('[limit_service]').remove();

            checkServices();
        }, 300);
    }

    function contractLimitsEditAddService(t)
    {
        var td = t.closest('td');

        /*if(td.find('[limit_service]').size()){
            message(0, 'Максимум один вид услуги');
            return false;
        }*/

        var tpl = $('<div class="form_elem" limit_service><nobr><select name="limit_service" onchange="checkServices()" /> <button class="btn btn_small btn_red btn_contract_limits_edit_del_serviсe" onclick="contractLimitsEditDelService($(this))">&times;</button></nobr></div>');

        var disabled = [
            $('.form_contract_limits_edit [name=limit_service]:first').val()
        ];
        $('.form_contract_limits_edit [name=limit_service]:first option:disabled').each(function () {
            disabled.push($(this).attr('value'));
        });

        if (disabled.length == services_cnt) {
            message(0, 'Доступные услуги закончились');
            return false;
        }

        for (var i in services) {
            tpl.find('select').append('<option value="' + i + '" '+ (disabled.indexOf(i) != -1 ? 'disabled' : '') +'>' + services[i] + '</option>');
        }

        if (td.find('[limit_service]').size()) {
            tpl.insertAfter(td.find('[limit_service]:last'));
        } else {
            tpl.insertBefore(td.find('div'));
        }

        checkServices();
    }

    function contractLimitsEditDelLimit(t)
    {
        if (confirm('Удалить весь лимит?')) {
            t.closest('[limit_group]').fadeOut();
            setTimeout(function () {
                t.closest('[limit_group]').remove();

                checkServices();
            }, 300);
        }
    }

    function contractLimitsEditCheckUnlim(t)
    {
        var td = t.closest('td');
        var check = td.find('[type=checkbox]');
        var limitGroup = t.closest('[limit_group]');
        var inputValue = limitGroup.find('[name=limit_value]');

        if (check.prop('checked')){
            inputValue.prop('disabled', true);
        } else {
            inputValue.prop('disabled', false);
        }
    }

    function contractLimitsEditAddLimit(t)
    {
        var table = t.closest('table');
        var tpl = $('<tr limit_group>' +
            '<td><div><nobr>' +
            '<button class="btn btn_small btn_green btn_contract_limits_edit_add_serviсe" onclick="contractLimitsEditAddService($(this))">+ добавить услугу</button>' +
            '<button class="btn btn_small btn_red btn_contract_limits_edit_del_limit" onclick="contractLimitsEditDelLimit($(this))">&times; удалить лимит</button>' +
            '</div></nobr></td>' +
            '<td class="v_top"><input type="text" name="limit_value" placeholder="Объем / сумма"></td>' +
            '<td class="v_top"><select name="limit_param" /></td>' +
            '<td class="v_top"><label><input name="limit_unlim" type="checkbox" onclick="contractLimitsEditCheckUnlim($(this))"> Без ограничений</label></td>' +
            '</tr>');

        for (var i in limitParams) {
            tpl.find('select[name=limit_param]').append('<option value="' + i + '">' + limitParams[i] + '</option>');
        }

        if (table.find('[limit_group]').size()) {
            tpl.insertAfter(table.find('[limit_group]:last'));
        } else {
            tpl.insertBefore(table.find('tr'));
        }

        renderCheckbox(tpl.find('[type=checkbox]'));
    }

    function contractLimitsEditGo(t)
    {
        var form = t.closest('.form_contract_limits_edit');
        var limits = [];

        var canEdit = true;

        $('[limit_group]', form).each(function(){
            var group_block = $(this);
            var group = {
                value:      $('[name=limit_value]', group_block).val(),
                param:      $('[name=limit_param]', group_block).val(),
                unlim:      $('[name=limit_unlim]', group_block).is(':checked') ? 1 : 0,
                services:   []
            };

            $('[name=limit_service]', group_block).each(function(){
                group.services.push($(this).val());
            });

            limits.push(group);

            if((group.value == '' && !group.unlim) || $('[name=limit_service]', group_block).size() == 0){
                canEdit = false;
            }
        });

        if(canEdit == false){
            message(0, 'Заполните данные корректно');
            return;
        }

        var params = {
            contract_id : $('[name=contracts_list]').val(),
            limits: limits,
            recalc: $("[name=recalc]", form).is(":checked") ? 1 : 0,
        };

        $.post('/clients/contract-limits-edit', params, function (data) {
            if (data.success) {
                message(1, 'Ограничения по договору успешно обновлены');
                loadContract('account');
            } else {
                message(0, 'Ошибка обновления ограничений');

                if(data.data){
                    for(var i in data.data){
                        message(0, data.data[i].text);
                    }
                }
            }
        });
    }

    function checkServices()
    {
        var form = $('.form_contract_limits_edit');

        var services = [];

        $('[name=limit_service]', form).each(function () {
            services.push($(this).val());
        });

        $('[name=limit_service]', form).each(function () {
            var select = $(this);
            var selectVal = select.val();

            select.find('option').each(function () {
                var option = $(this);
                var optionVal = option.attr('value');

                if (services.indexOf(optionVal) == -1 || optionVal == selectVal) {
                    option.prop('disabled', false);
                } else {
                    option.prop('disabled', true);
                }
            });
        });
    }
</script>