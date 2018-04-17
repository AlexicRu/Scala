<div class="tc_top_line">
    [<?=$contract['CONTRACT_ID']?>]
    <span toggle_block="block2">
        <?=$contract['CONTRACT_NAME']?> от <?=$contract['DATE_BEGIN']?> <?if($contract['DATE_END'] != '31.12.2099'){?>до <?=$contract['DATE_END']?><?}?> &nbsp;
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
            foreach(Model_Contract::$statusContractNames as $id => $name){
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
        <?if(1||Access::allow('view_payment_block')){?>
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
            <tr>
                <td class="gray right" width="130">Блокировка:</td>
                <td>
                    <span toggle_block="block2">
                        <?if($contractSettings['scheme'] == Model_Contract::PAYMENT_SCHEME_UNLIMITED){?>
                            Отсутствует
                        <?}else{?>
                            <?=$contractSettings['AUTOBLOCK_LIMIT']?>
                        <?}?>
                    </span>
                    <span toggle_block="block2" class="dn"><input type="text" name="AUTOBLOCK_LIMIT" class="input_small" value="<?=$contractSettings['AUTOBLOCK_LIMIT']?>" <?if ($contractSettings['scheme'] != Model_Contract::PAYMENT_SCHEME_LIMIT){echo 'disabled';}?>></span>
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
                    <?/*<span toggle_block="block2"><?=$contractSettings['INVOICE_PERIOD_VALUE'].' '.$period?></span>
                    <span toggle_block="block2" class="dn">
                        <select name="INVOICE_PERIOD_TYPE">
                            <?
                            foreach(Model_Contract::$invoicePeriods as $id => $value){
                                ?><option value="<?=$id?>" <?if($contractSettings['INVOICE_PERIOD_TYPE'] == $id){echo 'selected';}?>><?=$value?></option><?
                            }
                            ?>
                        </select>
                        <input type="text" name="INVOICE_PERIOD_VALUE" value="<?=$contractSettings['INVOICE_PERIOD_VALUE']?>">
                    </span>*/?>
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
            <?/*?>
        <fieldset class="inline_block">
            <legend>По карте</legend>
            <table>
                <tr>
                    <td class="gray right" width="200">Штрафы за маленькие обороты:</td>
                    <td>
                        <span toggle_block="block2"><input type="checkbox" class="switch" disabled></span>
                        <span toggle_block="block2" class="dn"><input type="checkbox" class="switch"></span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Оборот за период менее:</td>
                    <td>
                        <span toggle_block="block2">15000 <?=Text::RUR?></span>
                        <span toggle_block="block2" class="dn">
                            <input type="text" class="input_tiny" value="15000">
                            <select>
                                <option><?=Text::RUR?></option>
                                <option>Л</option>
                            </select>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Размер штрафа:</td>
                    <td>
                        <span toggle_block="block2">500</span>
                        <span toggle_block="block2" class="dn"><input type="text" class="input_small" value="500"></span>
                        <?=Text::RUR?>
                    </td>
                </tr>
            </table>
        </fieldset>
<?*/?>
        <?}?>
    </div>
    <div class="col line_inner">
        <?if(Access::allow('view_tariffs')){?>
            <b class="f18">Тарификация</b>
            <table>
                <tr>
                    <td class="gray right">Online тариф:</td>
                    <td>
                        <span toggle_block="block2"><?=$contractSettings['TARIF_NAME_ONLINE']?></span>
                        <span toggle_block="block2" class="dn">
                            <?=Form::buildField('contract_tariffs', 'TARIF_ONLINE', $contractSettings['TARIF_ONLINE'])?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Тариф по договору:</td>
                    <td>
                        <span toggle_block="block2"><?=$contractSettings['TARIF_NAME_OFFLINE']?></span>
                        <span toggle_block="block2" class="dn">
                            <?=Form::buildField('contract_tariffs', 'TARIF_OFFLINE', $contractSettings['TARIF_OFFLINE'])?>
                        </span>
                    </td>
                </tr>
            </table>
        <?}?>

        <br>
        <a href="#contract_history" class="btn fancy">История по договору</a>

        <?=$popupContractHistory?>
        
        <a href="#contract_notice_settings" class="btn fancy">Настройка уведомлений</a>

        <?=$popupContractNoticeSettings?>
    </div>
</div>

<script>
    $(function(){
        renderElements();
        renderTootip();

        $("select[name=scheme]").on('change', function(){
            var t = $(this);

            if(t.val() == 1){ //безлимит
                $("[name=AUTOBLOCK_LIMIT]").val(0).prop('disabled', true);
            }else if(t.val() == 2){ //предоплата
                $("[name=AUTOBLOCK_LIMIT]").val(0).prop('disabled', true);
            }else{ //порог отключения
                $("[name=AUTOBLOCK_LIMIT]").prop('disabled', false);
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
                        TARIF_OFFLINE:          getComboboxValue($('[name=TARIF_OFFLINE].combobox')),
                        AUTOBLOCK_LIMIT:        $("[name=AUTOBLOCK_LIMIT]").val(),
                        PENALTIES:              $("[name=PENALTIES]").val(),
                        OVERDRAFT:              $("[name=OVERDRAFT]").val(),
                        //INVOICE_PERIOD_TYPE:    $("[name=INVOICE_PERIOD_TYPE]").val(),
                        //INVOICE_PERIOD_VALUE:   $("[name=INVOICE_PERIOD_VALUE]").val(),
                        GOODS_RECIEVER:         getComboboxValue($("[name=GOODS_RECIEVER].combobox")),
                        CONTRACT_COMMENT:       $("[name=CONTRACT_COMMENT]").val(),
                        scheme:                 $("[name=scheme]").val()
                    }
                };


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

                        var contractFullName = "Договор: [<?=$contractSettings['CONTRACT_ID']?>] " + params.contract.CONTRACT_NAME + " от " + params.contract.DATE_BEGIN + (params.contract.DATE_END != '31.12.2099' ? " до " + params.contract.DATE_END : '');

                        $("[name=contracts_list] option:selected").text(contractFullName);

                        loadContract('contract');
                    }else{
                        message(0, 'Сохранение не удалось');
                    }
                });
            });
        <?}?>
    });
</script>