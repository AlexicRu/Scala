<?if (empty($clients)) {?>
    <div class="line_inner">Клиенты не найдены</div>
<?} else {?>

    <?foreach ($clients as $client) {?>
        <div class="line_inner" manager_id="<?=$managerId?>" client_id="<?=$client['CLIENT_ID']?>">
            <span class="gray"><?=$client['CLIENT_ID']?></span>
            &nbsp;&nbsp;&nbsp; <b><?=$client['CLIENT_NAME']?></b>
            <div class="fr">
                <a client_id="<?=$client['CLIENT_ID']?>" href="#" class="red del" onclick="delManagersClient($(this))">Удалить <i class="icon-cancel"></i></a>
            </div>

            <?if(Access::allow('managers_edit_manager_clients_contract_binds')) {?>
                <?if (!empty($contractsTree)) {?>
                    <div class="line_inner__second_line">
                        <table class="table_form">
                            <tr>
                                <td class="gray right">
                                    Договоры:
                                </td>
                                <td>
                                    <?
                                        $contractsIds = [];

                                        if (!empty($contractsTree[$client['CLIENT_ID']])) {
                                            $contractsIds = $contractsTree[$client['CLIENT_ID']];
                                        }
                                    ?>
                                    <?=Common::buildFormField('client_contract_choose_multi', 'manager_clients_contract_binds'.$client['CLIENT_ID'], implode(',', $contractsIds), ['client_id' => $client['CLIENT_ID']])?>
                                </td>
                                <td>
                                    <span class="btn btn_green" onclick="saveManagerClientContractBinds($(this))">Сохранить</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?}?>
            <?}?>
        </div>
    <?}?>
<?}?>