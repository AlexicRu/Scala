<script src="<?=Common::getAssetsLink()?>js/clients/client.js"></script>

<div class="back_link">&larr; <a href="/clients">Вернуться назад</a></div>
<h2>
    <span toggle_block="edit_client" uid="client_name"><?=$client['NAME']?></span>
    <span toggle_block="edit_client" uid="client_name" class="dn">
        <nobr>
            <input type="text" class="input_big input_grand" name="NAME" value="<?=Text::quotesForForms($client['NAME'])?>"
                <?=(!empty($client['NAME']) && Access::deny('edit_client_full') ? 'disabled' : '')?>
            >*
        </nobr>
    </span>
</h2>

<p>
    <span toggle_block="edit_client" uid="client_long_name"><?if($client['LONG_NAME']){?><?=$client['LONG_NAME']?><?}?></span>
    <span toggle_block="edit_client" uid="client_long_name" class="dn"><input type="text" class="input_grand" placeholder="Полное название" name="LONG_NAME" value="<?=Text::quotesForForms($client['LONG_NAME'])?>"></span>
</p>

<div toggle_block="block1" class="dn edit_client_block">
    <div class="col">
        <table>
            <tr>
                <td class="gray right" width="170">Юридический адрес:</td>
                <td width="370">
                    <span toggle_block="edit_client" uid="client_y_address"><?=($client['Y_ADDRESS'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_y_address" class="dn">
                        <nobr>
                            <input type="text" name="Y_ADDRESS" value="<?=$client['Y_ADDRESS']?>"
                                <?=(!empty($client['Y_ADDRESS']) && Access::deny('edit_client_full') ? 'disabled' : '')?>
                            >*
                        </nobr>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Фактический адрес:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_f_address"><?=($client['F_ADDRESS'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_f_address" class="dn"><input type="text" name="F_ADDRESS" value="<?=$client['F_ADDRESS']?>" ></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Почтовый адрес:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_p_address"><?=($client['P_ADDRESS'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_p_address" class="dn"><input type="text" name="P_ADDRESS" value="<?=$client['P_ADDRESS']?>"></span>
                </td>
            </tr>
            <tr>
                <td class="gray right" valign="top">Комментарий:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_comments"><?=($client['COMMENTS'] ? str_replace("\n", "<br>", $client['COMMENTS']) : '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_comments" class="dn"><textarea name="COMMENTS"><?=$client['COMMENTS']?></textarea></span>
                </td>
            </tr>
        </table>
    </div><div class="col">
        <table>
            <tr>
                <td class="gray right" width="170">Телефон:</td>
                <td width="200">
                    <span toggle_block="edit_client" uid="client_phone"><?=($client['PHONE'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_phone" class="dn"><nobr><input type="text" name="PHONE" value="<?=$client['PHONE']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">E-mail:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_email"><?=($client['EMAIL'] ? '<a href="mailto:'.$client['EMAIL'].'">'.$client['EMAIL'].'</a>' : '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_email" class="dn"><nobr><input type="text" name="EMAIL" value="<?=$client['EMAIL']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ИНН:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_inn"><?=($client['INN'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_inn" class="dn">
                        <nobr>
                            <input type="text" name="INN" value="<?=$client['INN']?>"
                                <?=(!empty($client['INN']) && Access::deny('edit_client_full') ? 'disabled' : '')?>
                            >*
                        </nobr>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gray right">КПП:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_kpp"><?=($client['KPP'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_kpp" class="dn">
                        <nobr>
                            <input type="text" name="KPP" value="<?=$client['KPP']?>"
                                <?=(!empty($client['KPP']) && Access::deny('edit_client_full') ? 'disabled' : '')?>
                            >*
                        </nobr>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ОГРН:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_ogrn"><?=($client['OGRN'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_ogrn" class="dn">
                        <input type="text" name="OGRN" value="<?=$client['OGRN']?>"
                            <?=(!empty($client['OGRN']) && Access::deny('edit_client_full') ? 'disabled' : '')?>
                        >
                    </span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ОКПО:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_okpo"><?=($client['OKPO'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_okpo" class="dn"><input type="text" name="OKPO" value="<?=$client['OKPO']?>"></span>
                </td>
            </tr>
        </table>
    </div>
    <br>
</div>

<div class="more_info dn" toggle_block="block1">
    <a href="#" class="btn btn_gray btn_min_width" toggle="block1">Скрыть информацию о компании</a> &nbsp;

    <?if(Access::allow('clients-client-edit')){?>
        <button class="btn" toggle="edit_client" toggle_block="edit_client"><i class="icon-pen"></i> Редактировать</button> &nbsp;
    <?}?>

    <?if(Access::allow('client_cabinet-create') && empty($client['EXISTS_OFFICE'])){?>
        <a href="#client_cabinet_create" class="btn btn_green fancy">+ Создать ЛК</a>
    <?}?>

    <button class="btn btn_green dn client_edit_btn btn_reverse" toggle_block="edit_client"><i class="icon-ok"></i> Сохранить</button>
    <button class="btn btn_red dn" toggle="edit_client" toggle_block="edit_client"><i class="icon-cancel"></i> Отмена</button>
</div>
<div class="more_info" toggle_block="block1">
    <a href="#" class="btn btn_gray btn_min_width" toggle="block1">Информация о компании</a>
</div>

<select name="contracts_list" class="select_big select_long" <?=(empty($contracts) ? 'disabled' :'')?>>
    <?if(empty($contracts)){?>
        <option value="0">Нет договоров</option>
    <?}else{
    foreach($contracts as $contract){?>
        <option value="<?=$contract['CONTRACT_ID']?>" <?=((!empty($contractId) && $contractId == $contract['CONTRACT_ID']) ? 'selected' : '')?>>
            Договор: [<?=$contract['CONTRACT_ID']?>] <?=$contract['CONTRACT_NAME']?> от <?=$contract['DATE_BEGIN']?> <?if($contract['DATE_END'] != '31.12.2099'){?>до <?=$contract['DATE_END']?><?}?>
        </option>
    <?}}?>
</select>

<?if(Access::allow('clients_contract-add')){?>
    &nbsp;&nbsp;&nbsp;<a href="#contract_add" class="btn fancy">+ Создать договор</a>

    <?=$popupContractAdd?>
<?}?>

<?if(Access::allow('client_cabinet-create')){?>
    <?=$popupCabinetCreate?>
<?}?>

<div class="ajax_contract_block"></div>

<script>
    var clientId = <?=$client['CLIENT_ID']?>;
</script>