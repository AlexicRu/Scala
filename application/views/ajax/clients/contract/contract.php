<div class="tc_top_line">
    [<?=$contract['CONTRACT_ID']?>]
    <span toggle_block="block2">
        <?=$contract['CONTRACT_NAME']?> от <?=$contract['DATE_BEGIN']?> <?if($contract['DATE_END'] != '31.12.2099'){?>до <?=$contract['DATE_END']?><?}?> &nbsp;
        <span class="label <?=Status::$statusContractClasses[$contract['STATE_ID']]?>"><?=Status::$statusContractNames[$contract['STATE_ID']]?></span>
    </span>
    <span toggle_block="block2" class="dn gray">
        <input type="text" name="CONTRACT_NAME" value="<?=$contract['CONTRACT_NAME']?>" class="input_big input_w_small">
        от
        <input type="text" name="DATE_BEGIN" value="<?=$contract['DATE_BEGIN']?>" class="input_big input_w_small datepicker" readonly>
        до
        <input type="text" name="DATE_END" value="<?=$contract['DATE_END']?>" class="input_big input_w_small datepicker" readonly>
        <select class="select_big" name="STATE_ID">
            <?
            foreach(Status::$statusContractNames as $id => $name){
                ?><option value="<?=$id?>" <?if($id == $contract['STATE_ID']){echo 'selected';}?>><?=$name?></option><?
            }
            ?>
        </select>
    </span>

    <?if(Access::allow('clients_contract_edit')){?>
        <div class="fr" toggle_block="block2"><button class="btn" toggle="block2"><i class="icon-pen"></i> Редактировать</button></div>
        <div class="fr dn" toggle_block="block2">
            <button class="btn btn_green btn_contract_save btn_reverse"><i class="icon-ok"></i> Сохранить</button>
            <button class="btn btn_red" toggle="block2"><i class="icon-cancel"></i> Отменить</button>
        </div>
    <?}?>
</div>
<div class="as_table">
    <div class="col">
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
                <td class="gray right">Переодичность выставления счетов:</td>
                <td>
                    <?
                    if($contractSettings['INVOICE_PERIOD_TYPE'] == Model_Contract::INVOICE_PERIOD_TYPE_DAY){
                        $period = Text::plural($contractSettings['INVOICE_PERIOD_VALUE'], ['день', 'дня', 'дней']);
                    }else{
                        $period = Text::plural($contractSettings['INVOICE_PERIOD_VALUE'], ['месяц', 'месяца', 'месяцев']);
                    }
                    ?>
                    <span toggle_block="block2"><?=$contractSettings['INVOICE_PERIOD_VALUE'].' '.$period?></span>
                    <span toggle_block="block2" class="dn">
                        <select name="INVOICE_PERIOD_TYPE">
                            <?
                            foreach(Model_Contract::$invoicePeriods as $id => $value){
                                ?><option value="<?=$id?>" <?if($contractSettings['INVOICE_PERIOD_TYPE'] == $id){echo 'selected';}?>><?=$value?></option><?
                            }
                            ?>
                        </select>
                        <input type="text" name="INVOICE_PERIOD_VALUE" value="<?=$contractSettings['INVOICE_PERIOD_VALUE']?>">
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Валюта:</td>
                <td>
                    Российский Рубль – <?=Text::RUR?>
                </td>
            </tr>
        </table>
        <br>
        <b class="f18">Ограничения по счету:</b>
        <table>
            <tr>
                <td class="gray right" width="160">Блокировка:</td>
                <td>
                    <span toggle_block="block2"><?=$contractSettings['AUTOBLOCK_LIMIT']?></span>
                    <span toggle_block="block2" class="dn"><input type="text" name="AUTOBLOCK_LIMIT" value="<?=$contractSettings['AUTOBLOCK_LIMIT']?>" <?if ($contractSettings['scheme'] != Model_Contract::PAYMENT_SCHEME_LIMIT){echo 'disabled';}?>></span>
                    <?=Text::RUR?>
                </td>
            </tr>
            <?if(Access::allow('view_penalties_overdrafts')){?>
            <tr>
                <td class="gray right">Пени:</td>
                <td>
                    <span toggle_block="block2"><?=$contractSettings['PENALTIES']?></span>
                    <span toggle_block="block2" class="dn"><input type="text" name="PENALTIES" value="<?=$contractSettings['PENALTIES']?>"></span>
                    %
                </td>
            </tr>
            <tr>
                <td class="gray right">Овердрафт:</td>
                <td>
                    <span toggle_block="block2"><?=$contractSettings['OVERDRAFT']?></span>
                    <span toggle_block="block2" class="dn"><input type="number" name="OVERDRAFT" min="0" value="<?=$contractSettings['OVERDRAFT']?>"></span>
                    <?=Text::RUR?>
                </td>
            </tr>
            <?}?>
        </table>
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
                            <select name="TARIF_ONLINE">
                                <?foreach($contractTariffs as $tariff){?>
                                    <option value="<?=$tariff['ID']?>" <?if($tariff['ID'] == $contractSettings['TARIF_ONLINE']){echo 'selected';}?>><?=$tariff['TARIF_NAME']?></option>
                                <?}?>
                            </select>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Offline тариф:</td>
                    <td>
                        <span toggle_block="block2"><?=$contractSettings['TARIF_NAME_OFFLINE']?></span>
                        <span toggle_block="block2" class="dn">
                            <select name="TARIF_OFFLINE">
                                <?foreach($contractTariffs as $tariff){?>
                                    <option value="<?=$tariff['ID']?>" <?if($tariff['ID'] == $contractSettings['TARIF_OFFLINE']){echo 'selected';}?>><?=$tariff['TARIF_NAME']?></option>
                                <?}?>
                            </select>
                        </span>
                    </td>
                </tr>
            </table>
        <?}?>

        <br>
        <a href="#contract_history" class="btn fancy">История по договору</a>

        <?=$popupContractHistory?>
        
        <?if(Access::allow('root')){?>
            <a href="#contract_notice_settings" class="btn fancy">Настройка оповещений</a>

            <?=$popupContractNoticeSettings?>
        <?}?>
    </div>
</div>

<script>
    $(function(){
        renderElements();

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

        <?if(Access::allow('clients_contract_edit')){?>
            $(".btn_contract_save").on('click', function(){
                var params = {
                    contract:{
                        CONTRACT_NAME:  $("[name=CONTRACT_NAME]").val(),
                        DATE_BEGIN:     $("[name=DATE_BEGIN]").val(),
                        DATE_END:       $("[name=DATE_END]").val(),
                        STATE_ID:       $("[name=STATE_ID]").val()
                    },
                    settings:{
                        TARIF_ONLINE:           $("[name=TARIF_ONLINE]").val(),
                        TARIF_OFFLINE:          $("[name=TARIF_OFFLINE]").val(),
                        AUTOBLOCK_LIMIT:        $("[name=AUTOBLOCK_LIMIT]").val(),
                        PENALTIES:              $("[name=PENALTIES]").val(),
                        OVERDRAFT:              $("[name=OVERDRAFT]").val(),
                        INVOICE_PERIOD_TYPE:    $("[name=INVOICE_PERIOD_TYPE]").val(),
                        INVOICE_PERIOD_VALUE:   $("[name=INVOICE_PERIOD_VALUE]").val(),
                        scheme:                 $("[name=scheme]").val()
                    }
                };

                if(params.contract.CONTRACT_NAME == ''){
                    $.jGrowl('Введите название', { header: 'Ошибка!' });
                    return false;
                }

                $.post('/clients/contract_edit/<?=$contractSettings['CONTRACT_ID']?>', {params:params}, function(data){
                    if(data.success){
                        $.jGrowl('Контракт обновлен', { header: 'Успех!' });

                        var contractFullName = "Договор: [<?=$contractSettings['CONTRACT_ID']?>] " + params.contract.CONTRACT_NAME + " от " + params.contract.DATE_BEGIN + (params.contract.DATE_END != '31.12.2099' ? " до " + params.contract.DATE_END : '');

                        $("[name=contracts_list] option:selected").text(contractFullName);

                        loadContract('contract');
                    }else{
                        $.jGrowl('Сохранение не удалось', { header: 'Ошибка!' });
                    }
                });
            });
        <?}?>
    });
</script>