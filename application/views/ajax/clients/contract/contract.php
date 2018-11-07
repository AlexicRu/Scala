<div class="tc_top_line">
    [<?=$contract['CONTRACT_ID']?>]
    <span toggle_block="block2">
        <?=$contract['CONTRACT_NAME']?> от <?=$contract['DATE_BEGIN']?> <?if($contract['DATE_END'] != Date::DATE_MAX){?>до <?=$contract['DATE_END']?><?}?> &nbsp;
        <span class="label <?=Model_Contract::$statusContractClasses[$contract['STATE_ID']]?>"><?=Model_Contract::$statusContractNames[$contract['STATE_ID']]?></span>
    </span>
    <span toggle_block="block2" class="dn gray">
        <input type="text" name="CONTRACT_NAME" value="<?=Text::quotesForForms($contract['CONTRACT_NAME'])?>" class="input_big input_medium">
        от
        <input type="text" name="DATE_BEGIN" value="<?=$contract['DATE_BEGIN']?>" class="input_big input_small datepicker" readonly>
        до
        <input type="text" name="DATE_END" value="<?=$contract['DATE_END']?>" class="input_big input_small datepicker" readonly>
        <select class="select_big" name="STATE_ID">
            <?
            $user = User::current();
            foreach(Model_Contract::$statusContractNames as $id => $name){
                if ($id == Model_Contract::STATE_CONTRACT_DELETED && !in_array($user['ROLE_ID'], Model_Contract::$stateContractDeletedRolesAccess)) {
                    continue;
                }
                ?><option value="<?=$id?>" <?if($id == $contract['STATE_ID']){echo 'selected';}?>><?=$name?></option><?
            }
            ?>
        </select>
    </span>

    <?if(Access::allow('clients_contract-edit')){?>
        <div class="fr" toggle_block="block2"><button class="btn" toggle="block2"><i class="icon-pen"></i> Редактировать</button></div>
        <div class="fr dn" toggle_block="block2">
            <button class="btn btn_green btn_contract_save btn_reverse"><i class="icon-ok"></i> Сохранить</button>
            <button class="btn btn_red" toggle="block2"><i class="icon-cancel"></i> Отменить</button>
        </div>
    <?}?>
</div>
<div class="as_table">
    <div class="col">
        <?if(Access::allow('view_payment_block')){?>
        <b class="f18">Оплата:</b>
        <table>
            <tr>
                <td class="gray right" width="160">Схема оплаты:</td>
                <td>
                    <span toggle_block="block2"><?=Model_Contract::$paymentSchemes[$contractSettings['scheme']]?></span>
                    <span toggle_block="block2" class="dn">
                        <select name="scheme">
                            <?
                            foreach(Model_Contract::$paymentSchemes as $id => $name){
                                ?><option value="<?=$id?>" <?if($id == $contractSettings['scheme']){echo 'selected';}?>><?=$name?></option><?
                            }
                            ?>
                        </select>
                    </span>
                </td>
            </tr>
            <tr class="contract-payment-scheme-limit-tr" <?if($contractSettings['scheme'] != Model_Contract::PAYMENT_SCHEME_LIMIT){?>style="display: none"<?}?>>
                <td class="gray right">Действует до:</td>
                <td>
                    <span toggle_block="block2">
                        <?if ($contractSettings['AUTOBLOCK_FLAG_DATE'] == Date::DATE_MAX) {?>
                            Бессрочно
                        <?} else {?>
                            <?=$contractSettings['AUTOBLOCK_FLAG_DATE']?>
                        <?}?>
                    </span>
                    <span toggle_block="block2" class="dn">
                        <input type="text" name="AUTOBLOCK_FLAG_DATE" class="datepicker" readonly
                               value="<?=($contractSettings['AUTOBLOCK_FLAG_DATE'] == Date::DATE_MAX ? '' : $contractSettings['AUTOBLOCK_FLAG_DATE'])?>"
                        >
                        <br>
                        <label>
                            <input type="checkbox" class="autoblock_flag_date_checkbox" onchange="checkAutoblockFlagDateIndefinitely($(this))"> Бессрочно
                        </label>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Блокировка:</td>
                <td>
                    <span toggle_block="block2">
                        <?if($contractSettings['scheme'] == Model_Contract::PAYMENT_SCHEME_UNLIMITED){?>
                            Отсутствует
                        <?}else{?>
                            <?=$contractSettings['AUTOBLOCK_LIMIT']?>
                        <?}?>
                    </span>
                    <span toggle_block="block2" class="dn"><input type="number" name="AUTOBLOCK_LIMIT" class="input_small" value="<?=$contractSettings['AUTOBLOCK_LIMIT']?>" <?if ($contractSettings['scheme'] != Model_Contract::PAYMENT_SCHEME_LIMIT){echo 'disabled';}?>></span>
                    <?if($contractSettings['scheme'] != Model_Contract::PAYMENT_SCHEME_UNLIMITED){?>
                        <?=Text::RUR?>
                    <?}?>
                </td>
            </tr>
            <tr>
                <td class="gray right">Периодичность выставления счетов:</td>
                <td>
                    <?
                    if($contractSettings['INVOICE_PERIOD_TYPE'] == Model_Contract::INVOICE_PERIOD_TYPE_DAY){
                        $period = Text::plural($contractSettings['INVOICE_PERIOD_VALUE'], ['день', 'дня', 'дней']);
                    }else{
                        $period = Text::plural($contractSettings['INVOICE_PERIOD_VALUE'], ['месяц', 'месяца', 'месяцев']);
                    }
                    ?>
                    <?=$contractSettings['INVOICE_PERIOD_VALUE'].' '.$period?>
                </td>
            </tr>
            <tr>
                <td class="gray right">Валюта:</td>
                <td>
                    Российский Рубль – <?=Text::RUR?>
                </td>
            </tr>
        </table>
        <?}?>

        <?if(Access::allow('view_goods_receiver')){?>
        <br>
        <b class="f18">Грузополучатель:</b><br>
        <table>
            <tr>
                <td class="gray right" width="160">Грузополучатель:</td>
                <td>
                    <span toggle_block="block2" class="goods_reciever_span"></span>
                    <span toggle_block="block2" class="dn">
                        <?=Form::buildField('client_choose_single', 'GOODS_RECIEVER', $contractSettings['GOODS_RECIEVER'], [
                            'render_value_to' => '.goods_reciever_span',
                        ])?>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Комментарий к договору:</td>
                <td>
                    <span toggle_block="block2"><?=($contractSettings['CONTRACT_COMMENT'] ?
                            Text::parseUrl($contractSettings['CONTRACT_COMMENT'])
                            : '<i class="gray">отсутствует</i>')?></span>
                    <span toggle_block="block2" class="dn">
                        <textarea name="CONTRACT_COMMENT"><?=$contractSettings['CONTRACT_COMMENT']?></textarea>
                    </span>
                </td>
            </tr>
        </table>
        <?}?>

        <?if(Access::allow('view_penalties')){?>
        <br>
        <b class="f18">Штрафы:</b><br>
        <fieldset class="inline_block">
            <legend>По счету</legend>
            <table>
                <tr>
                    <td class="gray right">Пени:</td>
                    <td>
                        <span toggle_block="block2"><?=$contractSettings['PENALTIES']?></span>
                        <span toggle_block="block2" class="dn"><input type="text" name="PENALTIES" class="input_small" value="<?=$contractSettings['PENALTIES']?>"></span>
                        %
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Овердрафт:</td>
                    <td>
                        <span toggle_block="block2"><?=$contractSettings['OVERDRAFT']?></span>
                        <span toggle_block="block2" class="dn"><input type="number" name="OVERDRAFT" class="input_small" min="0" value="<?=$contractSettings['OVERDRAFT']?>"></span>
                        <?=Text::RUR?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?}?>
    </div>
    <div class="col line_inner">
        <?if(Access::allow('view_tariffs')){?>
            <b class="f18">Тарификация</b>
            <table>
                <tr>
                    <td class="gray right" width="160">Online тариф:</td>
                    <td>
                        <span toggle_block="block2">[<?=$contractSettings['TARIF_ONLINE']?>] <?=$contractSettings['TARIF_NAME_ONLINE']?></span>
                        <span toggle_block="block2" class="dn">
                            <?=Form::buildField('contract_tariffs', 'TARIF_ONLINE', $contractSettings['TARIF_ONLINE'])?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Текущий тариф:</td>
                    <td>
                        [<?=$contractSettings['TARIF_OFFLINE']?>]  <?=$contractSettings['TARIF_NAME_OFFLINE']?>
                        <a href="#contract_tariff_edit" class="fancy btn btn_small btn_icon"><i class="icon-pen"></i></a>
                    </td>
                </tr>
            </table>

            <br>

            <b class="f18">История изменения тарифов</b>
            <div class="ajax_block_contract_tariff_history_out block_loading"></div>

            <br>
        <?}?>

        <a href="#contract_history" class="btn fancy">История по договору</a>

        <a href="#contract_notice_settings" class="btn fancy">Настройка уведомлений</a>

        <?if(Access::allow('view_contract_managers')){?>
        <br><br>
        <b class="f18">Менеджеры:</b><br>
        <table>
            <tr>
                <td class="gray right" width="160">Менеджер по продажам:</td>
                <td>
                    <?
                    $managers = [];
                    foreach ($contractManagers as $manager) {
                        if (in_array($manager['ROLE'], [Access::ROLE_MANAGER_SALE, Access::ROLE_MANAGER_SALE_SUPPORT])) {
                            $managers[] = $manager['MANAGER_NAME'];
                        }
                    }
                    if (empty($managers)) {
                        echo '<i class="gray">Не закреплен</i>';
                    } else {
                        echo implode(', ', $managers);
                    }?>
                </td>
            </tr>
            <tr>
                <td class="gray right">Менеджер по сопровождению:</td>
                <td>
                    <?
                    $managers = [];
                    foreach ($contractManagers as $manager) {
                        if (in_array($manager['ROLE'], [Access::ROLE_MANAGER, Access::ROLE_MANAGER_SALE_SUPPORT])) {
                            $managers[] = $manager['MANAGER_NAME'];
                        }
                    }
                    if (empty($managers)) {
                        echo '<i class="gray">Не закреплен</i>';
                    } else {
                        echo implode(', ', $managers);
                    }?>
                </td>
            </tr>
        </table>
        <?}?>
    </div>
</div>

<?=$popupContractHistory?>
<?=$popupContractNoticeSettings?>
<?=$popupContractTariffEdit?>

<script>
    $(function(){
        renderElements();
        renderTootip();
        $('[type=checkbox]').each(function(){
            renderCheckbox($(this));
        });
        $('.datepicker').each(function(){
            renderDatePicker($(this));
        });

        <?if(Access::allow('view_tariffs')){?>
            paginationAjax('/clients/get-contract-tariff-change-history/', 'ajax_block_contract_tariff_history', renderAjaxPaginationContractTariffHistory, {
                contract_id: <?=$contract['CONTRACT_ID']?>,
                emptyMessage: '<span class="gray">История изменения тарифа отсутствует</span>'
            });
        <?}?>

        <?if ($contractSettings['AUTOBLOCK_FLAG_DATE'] == Date::DATE_MAX) {?>
            $('.autoblock_flag_date_checkbox').prop('checked', true).trigger('change');
        <?}?>

        $("select[name=scheme]").on('change', function(){
            var t = $(this);
            var tr = $('.contract-payment-scheme-limit-tr');

            tr.hide();

            if(t.val() == 1){ //безлимит
                $("[name=AUTOBLOCK_LIMIT]").val(0).prop('disabled', true);
            }else if(t.val() == 2){ //предоплата
                $("[name=AUTOBLOCK_LIMIT]").val(0).prop('disabled', true);
            }else{ //порог отключения
                $("[name=AUTOBLOCK_LIMIT]").prop('disabled', false);

                tr.show();
            }
        });

        <?if(Access::allow('clients_contract-edit')){?>
            $(".btn_contract_save").on('click', function(){
                var params = {
                    contract:{
                        CONTRACT_NAME:  $("[name=CONTRACT_NAME]").val(),
                        DATE_BEGIN:     $("[name=DATE_BEGIN]").val(),
                        DATE_END:       $("[name=DATE_END]").val(),
                        STATE_ID:       $("[name=STATE_ID]").val()
                    },
                    settings:{
                        TARIF_ONLINE:           getComboboxValue($('[name=TARIF_ONLINE].combobox')),
                        AUTOBLOCK_LIMIT:        $("[name=AUTOBLOCK_LIMIT]").val(),
                        AUTOBLOCK_FLAG_DATE:    $("[name=AUTOBLOCK_FLAG_DATE]").prop('disabled') ? '<?=Date::DATE_MAX?>' : $("[name=AUTOBLOCK_FLAG_DATE]").val(),
                        PENALTIES:              $("[name=PENALTIES]").val(),
                        OVERDRAFT:              $("[name=OVERDRAFT]").val(),
                        GOODS_RECIEVER:         getComboboxValue($("[name=GOODS_RECIEVER].combobox")),
                        CONTRACT_COMMENT:       $("[name=CONTRACT_COMMENT]").val(),
                        scheme:                 $("[name=scheme]").val()
                    }
                };

                if (params.contract.STATE_ID == <?=Model_Contract::STATE_CONTRACT_DELETED?> && !confirm('Вы уверены, что хотите удалить договор?')) {
                    return false;
                }

                if(params.settings.TARIF_ONLINE == '' || params.settings.TARIF_OFFLINE == ''){
                    message(0, 'Заполните тарификацию');
                    return false;
                }

                if(params.contract.CONTRACT_NAME == ''){
                    message(0, 'Введите название');
                    return false;
                }

                $.post('/clients/contract-edit/<?=$contractSettings['CONTRACT_ID']?>', {params:params}, function(data){
                    if(data.success){
                        message(1, 'Контракт обновлен');

                        if (data.data.full_reload) {
                            setTimeout(function () {
                                window.location.reload();
                            }, 500);
                        } else {
                            var contractFullName = "Договор: [<?=$contractSettings['CONTRACT_ID']?>] " + params.contract.CONTRACT_NAME + " от " + params.contract.DATE_BEGIN + (params.contract.DATE_END != '<?=Date::DATE_MAX?>' ? " до " + params.contract.DATE_END : '');

                            $("[name=contracts_list] option:selected").text(contractFullName);

                            loadContract('contract');
                        }
                    }else{
                        message(0, 'Сохранение не удалось');
                    }
                });
            });
        <?}?>
    });

    function checkAutoblockFlagDateIndefinitely(checkbox)
    {
        if (checkbox.is(':checked')) {
            $('[name=AUTOBLOCK_FLAG_DATE]').prop('disabled', true).parent().find('img').hide();
        } else {
            $('[name=AUTOBLOCK_FLAG_DATE]').prop('disabled', false).parent().find('img').show();
        }
    }

    function renderAjaxPaginationContractTariffHistory(data, block)
    {
        for(var i in data){
            var tpl = $('<div class="line_inner">' +
                '<span class="gray th_data_from"> c <span /></span>' +
                '<span class="gray th_data_to">&nbsp;&nbsp;&nbsp; до <span /></span>' +
                '&nbsp;&nbsp;&nbsp;'+
                '<span class="th_name" />' +
                '</div>');

            tpl.find('.th_data_from span').text(data[i].DATE_FROM_STR);
            if (data[i].DATE_TO_STR != '<?=Date::DATE_MAX?>') {
                tpl.find('.th_data_to span').text(data[i].DATE_TO_STR);
            } else {
                tpl.find('.th_data_to').remove();
            }
            tpl.find('.th_name').text(data[i].TARIF_NAME);

            block.append(tpl);
        }
    }
</script>