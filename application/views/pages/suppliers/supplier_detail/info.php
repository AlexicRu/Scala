<div class="supplier-detail__name">
    <h2 class="supplier-detail__id">ID: <?=$supplier['ID']?></h2>

    <div class="supplier-detail__avatar <?=(empty($supplier['ICON_PATH']) ? 'supplier-detail__avatar-empty' : '')?>" <?=(!empty($supplier['ICON_PATH']) ? "style='background-image:url({$supplier['ICON_PATH']})'" : '')?>>
        <div toggle_block="edit_supplier" class="supplier-detail__avatar-pic"></div>
        <div toggle_block="edit_supplier" class="dn">
            <div class="dropzone supplier-detail__avatar-dropzone"></div>
        </div>
    </div>

    <h2 toggle_block="edit_supplier" uid="supplier_name"><?=$supplier['SUPPLIER_NAME']?></h2>
    <h2 toggle_block="edit_supplier" uid="supplier_name" class="dn"><input type="text" class="input_big input_grand" name="NAME" value="<?=$supplier['SUPPLIER_NAME']?>">*</h2>

    <p toggle_block="edit_supplier" uid="LONG_NAME"><?=$supplier['LONG_NAME']?></p>
    <p toggle_block="edit_supplier" uid="LONG_NAME" class="dn"><input type="text" class="input_grand" name="LONG_NAME" value="<?=$supplier['LONG_NAME']?>"></p>
</div>

<div toggle_block="supplier_info" class="dn">
    <div class="col">
        <table>
            <tr>
                <td class="gray right" width="170">Юридический адрес:</td>
                <td width="370">
                    <span toggle_block="edit_supplier" uid="supplier_y_address"><?=($supplier['Y_ADDRESS'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_y_address" class="dn"><nobr><input type="text" name="Y_ADDRESS" value="<?=$supplier['Y_ADDRESS']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Фактический адрес:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_f_address"><?=($supplier['F_ADDRESS'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_f_address" class="dn"><input type="text" name="F_ADDRESS" value="<?=$supplier['F_ADDRESS']?>" ></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Почтовый адрес:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_p_address"><?=($supplier['P_ADDRESS'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_p_address" class="dn"><input type="text" name="P_ADDRESS" value="<?=$supplier['P_ADDRESS']?>"></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Контактное лицо:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_contact_person"><?=($supplier['CONTACT_PERSON'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_contact_person" class="dn"><input type="text" name="CONTACT_PERSON" value="<?=$supplier['CONTACT_PERSON']?>"></span>
                </td>
            </tr>
            <tr>
                <td class="gray right" valign="top">Комментарий:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_comments"><?=($supplier['COMMENTS'] ? str_replace("\n", "<br>", $supplier['COMMENTS']) : '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_comments" class="dn"><textarea name="COMMENTS"><?=$supplier['COMMENTS']?></textarea></span>
                </td>
            </tr>
        </table>
    </div>
    <div class="col">
        <table>
            <tr>
                <td class="gray right" width="170">Телефон:</td>
                <td width="200">
                    <span toggle_block="edit_supplier" uid="supplier_phone"><?=($supplier['PHONE'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_phone" class="dn"><nobr><input type="text" name="PHONE" value="<?=$supplier['PHONE']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">E-mail:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_email"><?=($supplier['EMAIL'] ? '<a href="mailto:'.$supplier['EMAIL'].'">'.$supplier['EMAIL'].'</a>' : '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_email" class="dn"><nobr><input type="text" name="EMAIL" value="<?=$supplier['EMAIL']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ИНН:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_inn"><?=($supplier['INN'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_inn" class="dn"><nobr><input type="text" name="INN" value="<?=$supplier['INN']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">КПП:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_kpp"><?=($supplier['KPP'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_kpp" class="dn"><nobr><input type="text" name="KPP" value="<?=$supplier['KPP']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ОГРН:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_ogrn"><?=($supplier['OGRN'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_ogrn" class="dn"><input type="text" name="OGRN" value="<?=$supplier['OGRN']?>"></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ОКПО:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_okpo"><?=($supplier['OKPO'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_okpo" class="dn"><input type="text" name="OKPO" value="<?=$supplier['OKPO']?>"></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ОКОНХ:</td>
                <td>
                    <span toggle_block="edit_supplier" uid="supplier_okonh"><?=($supplier['OKONH'] ?: '<i class="gray">Не заполнено</i>')?></span>
                    <span toggle_block="edit_supplier" uid="supplier_okonh" class="dn"><input type="text" name="OKONH" value="<?=$supplier['OKONH']?>"></span>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="more_info dn" toggle_block="supplier_info">
    <a href="#" class="btn btn_gray btn_min_width" toggle="supplier_info">Скрыть информацию о поставщике</a> &nbsp;

    <button class="btn" toggle="edit_supplier" toggle_block="edit_supplier"><i class="icon-pen"></i> Редактировать</button> &nbsp;

    <button class="btn btn_green dn btn_reverse" toggle_block="edit_supplier" onclick="saveSupplierInfo(<?=$supplier['ID']?>)"><i class="icon-ok"></i> Сохранить</button>
    <button class="btn btn_red dn" toggle="edit_supplier" toggle_block="edit_supplier"><i class="icon-cancel"></i> Отмена</button>
</div>
<div class="more_info" toggle_block="supplier_info">
    <a href="#" class="btn btn_gray btn_min_width" toggle="supplier_info">Информация о поставщике</a>
</div>