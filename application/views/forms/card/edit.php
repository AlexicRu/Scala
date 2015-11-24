<table class="table_form form_card_edit">
    <tr>
        <td class="gray right" width="170">Держатель:</td>
        <td>
            <input type="text" name="card_edit_holder" class="input_big" value="<?=$card['HOLDER']?>" maxlength="200">
        </td>
    </tr>
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
                                <div class="form_elem" limit_service>
                                    <select name="limit_service">
                                        <?foreach($servicesList as $service){?>
                                            <option value="<?=$service['SERVICE_ID']?>" <?if($service['SERVICE_ID'] == $restrict['SERVICE_ID']){?>selected<?}?>><?=$service['LONG_DESC']?></option>
                                        <?}?>
                                    </select>
                                    <button class="btn btn_small btn_red btn_card_edit_del_serviсe">&times;</button>
                                </div>
                            <?}?>
                            <div>
                                <button class="btn btn_small btn_green btn_card_edit_add_serviсe">+ добавить услугу</button>
                                <button class="btn btn_small btn_red btn_card_edit_del_limit">&times; удалить лимит</button>
                            </div>
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
                    <td><button class="btn btn_green btn_card_edit_add_limit">+ Добавить ограничение</button></td>
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
            <span class="btn btn_reverse btn_card_edit_go">Сохранить</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        //чтобы не вешались несколько ивентов
        if(typeof(loaded_form_card_edit)  == "undefined") {
            loaded_form_card_edit = true;

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
            var limitTypes = {
                <?foreach(Model_Card::$cardLimitsTypes as $limitType => $value){?>
                "<?=$limitType?>": "<?=$value?>",
                <?}?>
            };

            $(document).on('click', '.btn_card_edit_go', function () {
                var t = $(this);
                var form = t.closest('.form_card_edit');
                var params = {
                    contract_id : $('[name=contracts_list]').val(),
                    card_id     : $('.tab_v.active').attr('tab'),
                    holder      : $('[name=card_edit_holder]', form).val(),
                    limits      : []
                };

                var canEdit = true;

                if($('[limit_group]', form).size() == 0){
                    canEdit = false;
                }

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

                    if(!group.value || $('[name=limit_service]', group_block).size() == 0){
                        canEdit = false;
                    }
                });

                if(canEdit == false){
                    message(0, 'Заполните данные корректно');
                    return;
                }

                $.post('/clients/card_edit', {params: params}, function (data) {
                    if (data.success) {
                        message(1, 'Карта успешно обновлена');
                        $.fancybox.close();
                        cardLoad($('.tab_v.active'), true);
                    } else {
                        message(0, 'Ошибка обновления карты');
                    }
                });
            });

            //удаление услуги
            $(document).on('click', ".btn_card_edit_del_serviсe", function () {
                var t = $(this);
                t.closest('[limit_service]').fadeOut();
                setTimeout(function () {
                    t.closest('[limit_service]').remove();
                }, 300);
            });
            //добавление услуги
            $(document).on('click', ".btn_card_edit_add_serviсe", function () {
                var t = $(this);
                var td = t.closest('td');
                var tpl = $('<div class="form_elem" limit_service><select name="limit_service" /><button class="btn btn_small btn_red btn_card_edit_del_serviсe">&times;</button></div>');

                for (var i in services) {
                    tpl.find('select').append('<option value="' + i + '">' + services[i] + '</option>');
                }

                if (td.find('[limit_service]').size()) {
                    tpl.insertAfter(td.find('[limit_service]:last'));
                } else {
                    tpl.insertBefore(td.find('div'));
                }
            });
            //удаление ограничения
            $(document).on('click', ".btn_card_edit_del_limit", function () {
                var t = $(this);
                if (confirm('Удалить весь лимит?')) {
                    t.closest('[limit_group]').fadeOut();
                    setTimeout(function () {
                        t.closest('[limit_group]').remove();
                    }, 300);
                }
            });
            //добавление ограничения
            $(document).on('click', ".btn_card_edit_add_limit", function () {
                var t = $(this);
                var table = t.closest('table');
                var tpl = $('<tr limit_group>' +
                    '<td><div>' +
                        '<button class="btn btn_small btn_green btn_card_edit_add_serviсe">+ добавить услугу</button>' +
                        '<button class="btn btn_small btn_red btn_card_edit_del_limit">&times; удалить лимит</button>' +
                    '</div></td>' +
                    '<td class="v_top"><input type="text" name="limit_value" placeholder="Объем / сумма"></td>' +
                    '<td class="v_top"><select name="limit_param" /></td><td class="v_top"><select name="limit_type" /></td>' +
                '</tr>');

                for (var i in limitParams) {
                    tpl.find('select[name=limit_param]').append('<option value="' + i + '">' + limitParams[i] + '</option>');
                }
                for (var j in limitTypes) {
                    tpl.find('select[name=limit_type]').append('<option value="' + j + '">' + limitTypes[j] + '</option>');
                }

                if (table.find('[limit_group]').size()) {
                    tpl.insertAfter(table.find('[limit_group]:last'));
                } else {
                    tpl.insertBefore(table.find('tr'));
                }
            });
        }
    });
</script>