<div class="supplier-contract__contract">
    <div class="tc_top_line">
        [<?=$contract['CONTRACT_ID']?>]
        <span toggle_block="toggle_contract">
            <?=$contract['CONTRACT_NAME']?> от <?=$contract['DATE_BEGIN']?> <?if($contract['DATE_END'] != '31.12.2099'){?>до <?=$contract['DATE_END']?><?}?> &nbsp;
            <span class="label <?=Model_Supplier_Contract::$statusContractClasses[$contract['CONTRACT_STATE']]?>"><?=Model_Supplier_Contract::$statusContractNames[$contract['CONTRACT_STATE']]?></span>
        </span>
        <span toggle_block="toggle_contract" class="dn gray">
            <input type="text" name="CONTRACT_NAME" value="<?=$contract['CONTRACT_NAME']?>" class="input_big input_medium">
            от
            <input type="text" name="DATE_BEGIN" value="<?=$contract['DATE_BEGIN']?>" class="input_big input_medium datepicker" readonly>
            до
            <input type="text" name="DATE_END" value="<?=$contract['DATE_END']?>" class="input_big input_medium datepicker" readonly>
            <select class="select_big" name="CONTRACT_STATE">
                <?
                foreach(Model_Supplier_Contract::$statusContractNames as $id => $name){
                    ?><option value="<?=$id?>" <?if($id == $contract['CONTRACT_STATE']){echo 'selected';}?>><?=$name?></option><?
                }
                ?>
            </select>
        </span>
    
        <?if(Access::allow('clients_contract_edit')){?>
            <div class="fr" toggle_block="toggle_contract"><button class="btn" toggle="toggle_contract"><i class="icon-pen"></i> Редактировать</button></div>
            <div class="fr dn" toggle_block="toggle_contract">
                <button class="btn btn_green btn_reverse" onclick="editSupplierContract()"><i class="icon-ok"></i> Сохранить</button>
                <button class="btn btn_red" toggle="toggle_contract"><i class="icon-cancel"></i> Отменить</button>
            </div>
        <?}?>
    </div>
    <div class="as_table">
        <div class="col">
            <table>
                <tr>
                    <td class="gray right" width="150">Валюта:</td>
                    <td>
                        Российский Рубль – <?=Text::RUR?>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Тип источника:</td>
                    <td>
                        <div toggle_block="toggle_contract">
                            <?if($contract['DATA_SOURCE'] == Model_Supplier_Contract::DATA_SOURCE_INSIDE){?>
                                Цепочка договоров внутри системы
                            <?}else{
                                foreach ($tubes as $tube) {
                                    if ($tube['PROJECT_ID'] == $contract['TUBE_ID']) {
                                        ?>Внешний - <b><?=$tube['PROJECT_NAME']?></b><?
                                    }
                                }
                            }?>
                        </div>
                        <div toggle_block="toggle_contract" class="dn">
                            <div class="supplier-contract__contract-data-source">
                                <label>
                                    <input type="radio" name="DATA_SOURCE" value="<?=Model_Supplier_Contract::DATA_SOURCE_INSIDE?>" <?=($contract['DATA_SOURCE'] == Model_Supplier_Contract::DATA_SOURCE_INSIDE ? 'checked' : '')?> onchange="checkSupplierContractDataSource()">
                                    Цепочка договоров внутри системы
                                </label>
                            </div>
                            <div class="supplier-contract__contract-data-source">
                                <label>
                                    <input type="radio" name="DATA_SOURCE" value="<?=Model_Supplier_Contract::DATA_SOURCE_OUTSIDE?>" <?=($contract['DATA_SOURCE'] == Model_Supplier_Contract::DATA_SOURCE_OUTSIDE ? 'checked' : '')?> onchange="checkSupplierContractDataSource()">
                                    Внешний источник
                                </label>
                                <select name="TUBE_ID" <?=($contract['DATA_SOURCE'] != Model_Supplier_Contract::DATA_SOURCE_OUTSIDE ? 'disabled' : '')?>>
                                    <?foreach ($tubes as $tube) {?>
                                        <option value="<?=$tube['PROJECT_ID']?>" <?=($tube['PROJECT_ID'] == $contract['TUBE_ID'] ? 'selected' : '')?>><?=$tube['PROJECT_NAME']?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                    </td>
                </tr>
                <?if(Access::allow('root')){?>
                <tr>
                    <td class="gray right">Услуги:</td>
                    <td>
                        <div toggle_block="toggle_contract" class="contract_service_render_value"></div>
                        <div class="dn" toggle_block="toggle_contract">
                            <?=Common::buildFormField('tariffs', 'service_choose_multi', 'CONTRACT_SERVICES', $contract['CONTRACT_SERVICES'], ['show_all' => true, 'render_value_to' => '.contract_service_render_value'])?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Группы точек:</td>
                    <td>
                        <div toggle_block="toggle_contract" class="contract_pos_groups_render_value"></div>
                        <div class="dn" toggle_block="toggle_contract">
                            <?=Common::buildFormField('tariffs', 'pos_group_choose_multi', 'CONTRACT_POS_GROUPS', $contract['CONTRACT_POS_GROUPS'], ['show_all' => true, 'render_value_to' => '.contract_pos_groups_render_value'])?>
                        </div>
                    </td>
                </tr>
                <?}?>
            </table>
        </div>
        <?if(Access::allow('root')){?>
        <div class="col line_inner">
            <b class="f18">Баланс по договору:</b>
            <br>
            <div class="f50"><b><?=number_format(138617, 2, ',', ' ')?></b> <?=Text::RUR?></div>
        </div>
        <?}?>
    </div>
</div>

<script>
    var DATA_SOURCE_INSIDE = <?=Model_Supplier_Contract::DATA_SOURCE_INSIDE?>;

    $(function () {
        $("input[type=radio]").each(function(){
            renderRadio($(this));
        });
    });
</script>