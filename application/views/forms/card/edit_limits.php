<?
$postfix = $card['CARD_ID'];
$systemId = $card['SYSTEM_ID'];

$canDelService = true;
$canAddService = true;
$canDelLimit = true;
$canAddLimit = true;
$canSave = true;
$editSelect = true;
$editServiceSelect = true;
$cntServiceForLimit = 1;
$cntServiceForFirstLimit = 1;
$limitParams = Model_Card::$cardLimitsParams;
$limitTypes = Model_Card::$cardLimitsTypes;
$cntTypes = false;

switch ($systemId) {
    case 1:
        $canDelService = false;
        $canAddService = false;
        $canDelLimit = false;
        $canAddLimit = false;
        $canSave = false;
        break;
    case 2:
        break;
    case 3:
        $canDelLimit = false;
        $canAddLimit = false;
        $canDelService = false;
        $canAddService = false;
        $editSelect = false;
        $editServiceSelect = false;
        break;
    case 4:
        $canDelService = false;
        $canAddService = false;
        $canDelLimit = false;
        $canAddLimit = false;
        $canSave = false;
        break;
    case 5:
        $cntServiceForFirstLimit = 999;
        $limitTypes = Model_Card::$cardLimitsTypesFull;
        $cntTypes = true;
        $editSelect = false;
        break;
    case 6:
        $cntServiceForLimit = 999;
        //можно все
        break;
    case 7:
    case 8:
        $limitParams = [
            Model_Card::CARD_LIMIT_PARAM_RUR => $limitParams[Model_Card::CARD_LIMIT_PARAM_RUR]
        ];
        $limitTypes = [
            Model_Card::CARD_LIMIT_PARAM_RUR => $limitTypes[Model_Card::CARD_LIMIT_TYPE_DAY]
        ];
        break;
}

if(!empty($card['CHANGE_LIMIT_AVAILABLE']) && Access::allow('clients_card-edit-limits')){?>

    <table class="table_form form_card_edit form_card_edit_<?=$postfix?>">
        <tr>
            <td class="gray right v_top" width="170">Ограничения по топливу:</td>
            <td>
                <table class="table_form table_form_limits">
                    <?foreach($oilRestrictions as $restriction){
                        ?>
                        <tr limit_group="<?=$restriction['LIMIT_ID']?>">
                            <td>
                                <?foreach($restriction['services'] as $restrictionService){?>
                                    <div class="form_elem" limit_service>
                                        <nobr>
                                            <select name="limit_service" onchange="checkServices_<?=$postfix?>()" <?=(empty($editServiceSelect) ? 'disabled' : '')?>>
                                                <?foreach($servicesList as $service){?>
                                                    <option
                                                            group="<?=$service['SYSTEM_SERVICE_GROUP']?>"
                                                            value="<?=$service['SERVICE_ID']?>"
                                                            <?if($service['SERVICE_ID'] == $restrictionService['id']){?>selected<?}?>
                                                    ><?=$service['FOREIGN_DESC']?></option>
                                                <?}?>
                                            </select>

                                            <?if ($canDelService) {?>
                                                <button class="btn btn_small btn_red btn_card_edit_del_serviсe" onclick="cardEditDelService_<?=$postfix?>($(this))">&times;</button>
                                            <?}?>
                                        </nobr>
                                    </div>
                                <?}?>
                                <div>
                                    <nobr>
                                        <?if ($canAddService) {?>
                                            <button class="btn btn_small btn_green btn_card_edit_add_serviсe" onclick="cardEditAddService_<?=$postfix?>($(this))">+ добавить услугу</button>
                                        <?}?>
                                        <?if ($canDelLimit) {?>
                                            <button class="btn btn_small btn_red btn_card_edit_del_limit" onclick="cardEditDelLimit_<?=$postfix?>($(this))">&times; удалить лимит</button>
                                        <?}?>
                                    </nobr>
                                </div>
                            </td>
                            <td class="v_top">
                                <input type="text" name="limit_value" value="<?=$restriction['LIMIT_VALUE']?>" placeholder="Объем / сумма" class="input_mini">
                            </td>
                            <td class="v_top">
                                <select name="unit_type" <?=(empty($editSelect) ? 'disabled' : '')?>>
                                    <?foreach($limitParams as $limitParam => $value){?>
                                        <option value="<?=$limitParam?>" <?if($limitParam == $restriction['UNIT_TYPE']){?>selected<?}?>><?=$value?></option>
                                    <?}?>
                                </select>
                            </td>
                            <?if ($cntTypes) {?>
                                <td class="v_top">
                                     <input type="text" name="duration_value" value="<?=$restriction['DURATION_VALUE']?>" placeholder="Кол-во" class="input_mini" disabled>
                                </td>
                            <?}?>
                            <td class="v_top">
                                <select name="duration_type" <?=(empty($editSelect) ? 'disabled' : '')?>>
                                    <?foreach($limitTypes as $limitType => $value){?>
                                        <option value="<?=$limitType?>" <?if($limitType == $restriction['DURATION_TYPE']){?>selected<?}?>><?=$value?></option>
                                    <?}?>
                                </select>
                            </td>
                        </tr>
                    <?}?>
                    <?if ($canAddLimit) {?>
                        <tr>
                            <td><button class="btn btn_green btn_card_edit_add_limit" onclick="cardEditAddLimit_<?=$postfix?>($(this))">+ Добавить ограничение</button></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?if ($cntTypes) {?><td></td><?}?>
                        </tr>
                    <?}?>
                </table>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <?if ($canSave) {?>
                    <span class="btn btn_reverse" onclick="cardEditGo_<?=$postfix?>($(this))"><i class="icon-ok"></i> Сохранить лимиты</span>
                <?}?>
                <span class="btn btn_red fancy_close">Отмена</span>
            </td>
        </tr>
    </table>
<?}?>

<script>
    $(function () {
        $('[name=card_edit_date]').each(function () {
            renderDatePicker($(this));
        });
        checkServices_<?=$postfix?>();
    });

    var services_cnt_<?=$postfix?> = <?=count($servicesList)?>;
    var services_<?=$postfix?> = {
        <?foreach($servicesList as $service){?>
            "<?=$service['SERVICE_ID']?>": {
                name: "<?=$service['FOREIGN_DESC']?>",
                group: "<?=$service['SYSTEM_SERVICE_GROUP']?>"
            },
        <?}?>
    };
    var limitParams_<?=$postfix?> = {
        <?foreach($limitParams as $limitParam => $value){?>
        "<?=$limitParam?>": "<?=$value?>",
        <?}?>
    };
    var limitTypes_<?=$postfix?> = {
        <?foreach($limitTypes as $limitType => $value){?>
        "<?=$limitType?>": "<?=$value?>",
        <?}?>
    };

    function cardEditDelService_<?=$postfix?>(t)
    {
        t.closest('[limit_service]').fadeOut();
        setTimeout(function () {
            t.closest('[limit_service]').remove();

            checkServices_<?=$postfix?>();
        }, 300);
    }

    function cardEditAddService_<?=$postfix?>(t)
    {
        var td = t.closest('td');

        <?if ($cntServiceForLimit != $cntServiceForFirstLimit) {?>
            var tr = t.closest('tr');

            if (tr.index() == 0) {
                if (td.find('[limit_service]').length == <?=$cntServiceForFirstLimit?>) {
                    message(0, 'Максимум услуг на первый лимит: <?=$cntServiceForFirstLimit?>');
                    return false;
                }
            } else {
                if(td.find('[limit_service]').length == <?=$cntServiceForLimit?>){
                    message(0, 'Максимум услуг на лимит: <?=$cntServiceForLimit?>');
                    return false;
                }
            }
        <?} else {?>
            if(td.find('[limit_service]').length == <?=$cntServiceForLimit?>){
                message(0, 'Максимум услуг на лимит: <?=$cntServiceForLimit?>');
                return false;
            }
        <?}?>

        var tpl = $('<div class="form_elem" limit_service><nobr><select name="limit_service" onchange="checkServices_<?=$postfix?>()" /> '+
            <?if ($canDelService){?>'<button class="btn btn_small btn_red btn_card_edit_del_serviсe" onclick="cardEditDelService_<?=$postfix?>($(this))">&times;</button>'+<?}?>
            '</nobr></div>');

        var selectFirst = $('.form_card_edit_<?=$postfix?> [name=limit_service]:first');
        var group = selectFirst.find('option:selected').attr('group');
        var disabled = [
            selectFirst.val()
        ];
        $('option', selectFirst).each(function () {
            var t = $(this);
            if (t.is(":disabled") || t.attr('group') != group) {
                disabled.push($(this).attr('value'));
            }
        });

        if (disabled.length == services_cnt_<?=$postfix?>) {
            message(0, 'Доступные услуги закончились');
            return false;
        }

        for (var i in services_<?=$postfix?>) {
            var service = services_<?=$postfix?>[i];
            tpl.find('select').append('<option value="' + i + '" '+ (disabled.indexOf(i) != -1 ? 'disabled' : '') +' group="'+ service.group +'">' + service.name + '</option>');
        }

        if (td.find('[limit_service]').size()) {
            tpl.insertAfter(td.find('[limit_service]:last'));
        } else {
            tpl.insertBefore(td.find('div'));
        }

        checkServices_<?=$postfix?>();
    }

    function cardEditDelLimit_<?=$postfix?>(t)
    {
        if (confirm('Удалить весь лимит?')) {
            t.closest('[limit_group]').fadeOut();
            setTimeout(function () {
                t.closest('[limit_group]').remove();

                checkServices_<?=$postfix?>();
            }, 300);
        }
    }

    function cardEditAddLimit_<?=$postfix?>(t)
    {
        var table = t.closest('table');
        var tpl = $('<tr limit_group="-1">' +
            '<td><div><nobr>' +
            <?if ($canAddService){?>'<button class="btn btn_small btn_green btn_card_edit_add_serviсe" onclick="cardEditAddService_<?=$postfix?>($(this))">+ добавить услугу</button>' +<?}?>
            <?if ($canDelLimit){?>'<button class="btn btn_small btn_red btn_card_edit_del_limit" onclick="cardEditDelLimit_<?=$postfix?>($(this))">&times; удалить лимит</button>' +<?}?>
            '</div></nobr></td>' +
            '<td class="v_top"><input type="text" name="limit_value" class="input_mini" placeholder="Объем / сумма"></td>' +
            '<td class="v_top"><select name="unit_type" /></td>'+
            <?if ($cntTypes) {?>
            '<td class="v_top"><input type="text" name="duration_value" placeholder="Кол-во" class="input_mini" /></td>' +
            <?}?>
            '<td class="v_top"><select name="duration_type" /></td>' +
            '</tr>');

        for (var i in limitParams_<?=$postfix?>) {
            tpl.find('select[name=unit_type]').append('<option value="' + i + '">' + limitParams_<?=$postfix?>[i] + '</option>');
        }
        for (var j in limitTypes_<?=$postfix?>) {
            tpl.find('select[name=duration_type]').append('<option value="' + j + '">' + limitTypes_<?=$postfix?>[j] + '</option>');
        }

        if (table.find('[limit_group]').size()) {
            tpl.insertAfter(table.find('[limit_group]:last'));
        } else {
            tpl.insertBefore(table.find('tr'));
        }
    }

    function cardEditGo_<?=$postfix?>(t)
    {
        var form = t.closest('.form_card_edit');
        var params = {
            contract_id : $('[name=contracts_list]').val(),
            card_id     : $('.tab_v.active').attr('tab'),
            limits      : []
        };

        var canEdit = true;

        /*if($('[limit_group]', form).size() == 0){
         canEdit = false;
         }*/

        $('[limit_group]', form).each(function(){
            var group_block = $(this);
            var group = {
                limit_id:       group_block.attr('limit_group'),
                value:          $('[name=limit_value]', group_block).val(),
                unit_type:      $('[name=unit_type]', group_block).val(),
                duration_type:  $('[name=duration_type]', group_block).val(),
                duration_value: $('[name=duration_value]', group_block).val(),
                services:       []
            };

            $('[name=limit_service]', group_block).each(function(){
                group.services.push($(this).val());
            });

            params.limits.push(group);

            if(group.value == '' || $('[name=limit_service]', group_block).size() == 0){
                canEdit = false;
            }
        });

        if(canEdit == false){
            message(0, 'Заполните данные корректно');
            return;
        }

        $.post('/clients/card-edit-limits', params, function (data) {
            if (data.success) {
                message(1, 'Лимиты карты успешно обновлена');
                $.fancybox.close();
                cardLoad($('.tab_v.active'), true);
            } else {
                message(0, 'Ошибка обновления лимитов карты');

                if(data.data){
                    for(var i in data.data){
                        message(0, data.data[i].text);
                    }
                }
            }
        });
    }

    function checkServices_<?=$postfix?>()
    {
        var form = $('.form_card_edit_<?=$postfix?>');

        var services = [];

        $('[name=limit_service]', form).each(function () {
            services.push($(this).val());
        });

        $('[name=limit_service]', form).each(function () {
            var select = $(this);
            var selectVal = select.val();
            var group = select.closest('td').find('[name=limit_service]:first option:selected').attr('group');
            var cnt = select.closest('td').find('[name=limit_service]').length;

            select.find('option').each(function () {
                var option = $(this);
                var optionVal = option.attr('value');
                var optionGroup = option.attr('group');

                if ((services.indexOf(optionVal) == -1 || optionVal == selectVal) && (optionGroup == group || cnt == 1)) {
                    option.prop('disabled', false);
                } else {
                    option.prop('disabled', true);
                }
            });
        });
    }
</script>