<?
$postfix = $card['CARD_ID'];
?>
    <?if(!empty($card['CHANGE_LIMIT_AVAILABLE']) && Access::allow('clients_card_edit_limits')){?>

<table class="table_form form_card_edit">
    <tr>
        <td class="gray right v_top" width="170">Ограничения по топливу:</td>
        <td>
            <table class="table_form table_form_limits">
                <?foreach($oilRestrictions as $restrictions){
                    $restriction = reset($restrictions);
                    ?>
                    <tr limit_group>
                        <td>
                            <?foreach($restrictions as $restrict){?>
                                <div class="form_elem" limit_service><nobr>
                                    <select name="limit_service">
                                        <?foreach($servicesList as $service){?>
                                            <option value="<?=$service['SERVICE_ID']?>" <?if($service['SERVICE_ID'] == $restrict['SERVICE_ID']){?>selected<?}?>><?=$service['FOREIGN_DESC']?></option>
                                        <?}?>
                                    </select>
                                    <button class="btn btn_small btn_red btn_card_edit_del_serviсe" onclick="cardEditDelService_<?=$postfix?>($(this))">&times;</button>
                                </nobr></div>
                            <?}?>
                            <div><nobr>
                                <button class="btn btn_small btn_green btn_card_edit_add_serviсe" onclick="cardEditAddService_<?=$postfix?>($(this))">+ добавить услугу</button>
                                <button class="btn btn_small btn_red btn_card_edit_del_limit" onclick="cardEditDelLimit_<?=$postfix?>($(this))">&times; удалить лимит</button>
                            </nobr></div>
                        </td>
                        <td class="v_top">
                            <input type="text" name="limit_value" value="<?=$restriction['LIMIT_VALUE']?>" placeholder="Объем / сумма">
                        </td>
                        <td class="v_top">
                            <select name="limit_param">
                                <?foreach(Model_Card::$cardLimitsParams as $limitParam => $value){?>
                                    <option value="<?=$limitParam?>" <?if($limitParam == $restriction['LIMIT_PARAM']){?>selected<?}?>><?=$value?></option>
                                <?}?>
                            </select>
                        </td>
                        <td class="v_top">
                            <select name="limit_type">
                                <?foreach(Model_Card::$cardLimitsTypes as $limitType => $value){?>
                                    <option value="<?=$limitType?>" <?if($limitType == $restriction['LIMIT_TYPE']){?>selected<?}?>><?=$value?></option>
                                <?}?>
                            </select>
                        </td>
                    </tr>
                <?}?>
                <tr>
                    <td><button class="btn btn_green btn_card_edit_add_limit" onclick="cardEditAddLimit_<?=$postfix?>($(this))">+ Добавить ограничение</button></td>
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
            <span class="btn btn_reverse" onclick="cardEditGo_<?=$postfix?>($(this))"><i class="icon-ok"></i> Сохранить лимиты</span>
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
    });

    var services_<?=$postfix?> = {
        <?foreach($servicesList as $service){?>
        "<?=$service['SERVICE_ID']?>": "<?=$service['FOREIGN_DESC']?>",
        <?}?>
    };
    var limitParams_<?=$postfix?> = {
        <?foreach(Model_Card::$cardLimitsParams as $limitParam => $value){?>
        "<?=$limitParam?>": "<?=$value?>",
        <?}?>
    };
    var limitTypes_<?=$postfix?> = {
        <?foreach(Model_Card::$cardLimitsTypes as $limitType => $value){?>
        "<?=$limitType?>": "<?=$value?>",
        <?}?>
    };

    function cardEditDelService_<?=$postfix?>(t)
    {
        t.closest('[limit_service]').fadeOut();
        setTimeout(function () {
            t.closest('[limit_service]').remove();
        }, 300);
    }

    function cardEditAddService_<?=$postfix?>(t)
    {
        var td = t.closest('td');

        if(td.find('[limit_service]').size()){
            message(0, 'Максимум один вид услуги');
            return false;
        }

        var tpl = $('<div class="form_elem" limit_service><nobr><select name="limit_service" /> <button class="btn btn_small btn_red btn_card_edit_del_serviсe" onclick="cardEditDelService_<?=$postfix?>($(this))">&times;</button></nobr></div>');

        for (var i in services_<?=$postfix?>) {
            tpl.find('select').append('<option value="' + i + '">' + services_<?=$postfix?>[i] + '</option>');
        }

        if (td.find('[limit_service]').size()) {
            tpl.insertAfter(td.find('[limit_service]:last'));
        } else {
            tpl.insertBefore(td.find('div'));
        }
    }

    function cardEditDelLimit_<?=$postfix?>(t)
    {
        if (confirm('Удалить весь лимит?')) {
            t.closest('[limit_group]').fadeOut();
            setTimeout(function () {
                t.closest('[limit_group]').remove();
            }, 300);
        }
    }

    function cardEditAddLimit_<?=$postfix?>(t)
    {
        var table = t.closest('table');
        var tpl = $('<tr limit_group>' +
            '<td><div><nobr>' +
            '<button class="btn btn_small btn_green btn_card_edit_add_serviсe" onclick="cardEditAddService_<?=$postfix?>($(this))">+ добавить услугу</button>' +
            '<button class="btn btn_small btn_red btn_card_edit_del_limit" onclick="cardEditDelLimit_<?=$postfix?>($(this))">&times; удалить лимит</button>' +
            '</div></nobr></td>' +
            '<td class="v_top"><input type="text" name="limit_value" placeholder="Объем / сумма"></td>' +
            '<td class="v_top"><select name="limit_param" /></td><td class="v_top"><select name="limit_type" /></td>' +
            '</tr>');

        for (var i in limitParams_<?=$postfix?>) {
            tpl.find('select[name=limit_param]').append('<option value="' + i + '">' + limitParams_<?=$postfix?>[i] + '</option>');
        }
        for (var j in limitTypes_<?=$postfix?>) {
            tpl.find('select[name=limit_type]').append('<option value="' + j + '">' + limitTypes_<?=$postfix?>[j] + '</option>');
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
                value:      $('[name=limit_value]', group_block).val(),
                param:      $('[name=limit_param]', group_block).val(),
                type:       $('[name=limit_type]', group_block).val(),
                services:   []
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

        $.post('/clients/card_edit_limits', params, function (data) {
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
</script>