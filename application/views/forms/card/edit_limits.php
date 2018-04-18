<?
$postfix = $card['CARD_ID'];

if(!empty($card['CHANGE_LIMIT_AVAILABLE']) && Access::allow('clients_card-edit-limits')){?>

    <table class="table_form form_card_edit form_card_edit_<?=$postfix?>">
        <tr>
            <td class="gray right v_top" width="170">Ограничения по топливу:</td>
            <td>
                <table class="table_form table_form_limits">
                    <?foreach($oilRestrictions as $restriction){
                        echo Form::buildLimit($card['CARD_ID'], $restriction, $postfix);
                    }?>
                    <?if ($settings['canAddLimit']) {?>
                        <tr>
                            <td><button class="btn btn_green btn_card_edit_add_limit" onclick="cardEditAddLimit_<?=$postfix?>($(this))">+ Добавить ограничение</button></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <?if ($settings['cntTypes']) {?><td></td><?}?>
                        </tr>
                    <?}?>
                </table>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <?if ($settings['canSave']) {?>
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

        <?if ($settings['cntServiceForLimit'] != $settings['cntServiceForFirstLimit']) {?>
            var tr = t.closest('tr');

            if (tr.index() == 0) {
                if (td.find('[limit_service]').length == <?=$settings['cntServiceForFirstLimit']?>) {
                    message(0, 'Максимум услуг на первый лимит: <?=$settings['cntServiceForFirstLimit']?>');
                    return false;
                }
            } else {
                if(td.find('[limit_service]').length == <?=$settings['cntServiceForLimit']?>){
                    message(0, 'Максимум услуг на лимит: <?=$settings['cntServiceForLimit']?>');
                    return false;
                }
            }
        <?} else {?>
            if(td.find('[limit_service]').length == <?=$settings['cntServiceForLimit']?>){
                message(0, 'Максимум услуг на лимит: <?=$settings['cntServiceForLimit']?>');
                return false;
            }
        <?}?>

        var params = {
            cardId:     '<?=$card['CARD_ID']?>',
            postfix:    '<?=$postfix?>'
        };

        $.get('/clients/card-limit-service-template/', params, function(tpl){
            tpl = $(tpl);
            var selectFirst = $('.form_card_edit_<?=$postfix?> [name=limit_service]:first');
            var group = selectFirst.find('option:selected').attr('group');
            var disabled = [
                selectFirst.val()
            ];
            $('option', selectFirst).each(function () {
                var t = $(this);
                if (t.is(":disabled") || t.attr('group') != group) {
                    disabled.push(t.attr('value'));
                }
            });

            if (disabled.length == services_cnt_<?=$postfix?>) {
                message(0, 'Доступные услуги закончились');
                return false;
            }

            var flSetSelected = false;
            for (var i in services_<?=$postfix?>) {
                if (disabled.indexOf(i) != -1) {
                    tpl.find('select option[value="'+ i +'"]').prop('disabled', true);
                } else {
                    flSetSelected = true;
                    tpl.find('select option[value="'+ i +'"]').prop('selected', true);
                }
            }

            if (td.find('[limit_service]').size()) {
                tpl.insertAfter(td.find('[limit_service]:last'));
            } else {
                tpl.insertBefore(td.find('div'));
            }

            checkServices_<?=$postfix?>();
        });
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

        if ($('tr[limit_group]', table).length >= <?=$settings['cntLimits']?>) {
            message(0, 'Достигнуто максимальное кол-во лимитов');
            return false;
        }

        var params = {
            cardId:     '<?=$card['CARD_ID']?>',
            postfix:    '<?=$postfix?>'
        };

        $.get('/clients/card-limit-template/', params, function(tpl){
            tpl = $(tpl);
            if (table.find('[limit_group]').size()) {
                tpl.insertAfter(table.find('[limit_group]:last'));
            } else {
                tpl.insertBefore(table.find('tr'));
            }
        });
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
                    message(0, data.data);
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