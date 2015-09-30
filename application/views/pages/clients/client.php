<script src="/js/clients/client.js"></script>

<a href="/clients" class="back_link">&larr; Вернуться назад</a>

<h2>
    <span toggle_block="edit_client" uid="client_name"><?=$client['NAME']?></span>
    <span toggle_block="edit_client" uid="client_name" class="dn"><nobr><input type="text" class="input_big" name="NAME" value="<?=$client['NAME']?>">*</nobr></span>
</h2>

<?if($client['LONG_NAME']){?>
    <p>
        <span toggle_block="edit_client" uid="client_long_name"><?=$client['LONG_NAME']?></span>
        <span toggle_block="edit_client" uid="client_long_name" class="dn"><input type="text" name="LONG_NAME" value="<?=$client['LONG_NAME']?>"></span>
    </p>
<?}?>

<div toggle_block="block1" class="dn edit_client_block">
    <div class="col">
        <table>
            <tr>
                <td class="gray right" width="170">Юридический адрес:</td>
                <td width="370">
                    <span toggle_block="edit_client" uid="client_y_address"><?=($client['Y_ADDRESS'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_y_address" class="dn"><nobr><input type="text" name="Y_ADDRESS" value="<?=$client['Y_ADDRESS']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">Фактический адрес:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_f_address"><?=($client['F_ADDRESS'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_f_address" class="dn"><input type="text" name="F_ADDRESS" value="<?=$client['F_ADDRESS']?>"></span>
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
                    <span toggle_block="edit_client" uid="client_email"><?=($client['EMAIL'] ? '<a href="mailto'.$client['EMAIL'].'">'.$client['EMAIL'].'</a>' : '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_email" class="dn"><nobr><input type="text" name="EMAIL" value="<?=$client['EMAIL']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ИНН:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_inn"><?=($client['INN'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_inn" class="dn"><nobr><input type="text" name="INN" value="<?=$client['INN']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">КПП:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_kpp"><?=($client['KPP'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_kpp" class="dn"><nobr><input type="text" name="KPP" required value="<?=$client['KPP']?>">*</nobr></span>
                </td>
            </tr>
            <tr>
                <td class="gray right">ОГРН:</td>
                <td>
                    <span toggle_block="edit_client" uid="client_ogrn"><?=($client['OGRN'] ?: '<span class="gray">Не заполнено</span>')?></span>
                    <span toggle_block="edit_client" uid="client_ogrn" class="dn"><input type="text" name="OGRN" value="<?=$client['OGRN']?>"></span>
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

    <button class="btn" toggle="edit_client" toggle_block="edit_client"><i class="icon-pen"></i> Редактировать</button>

    <button class="btn btn_red dn" toggle="edit_client" toggle_block="edit_client"><i class="icon-cancel"></i> Отмена</button>
    <button class="btn btn_green dn client_edit_btn" toggle_block="edit_client"><i class="icon-ok"></i> Сохранить</button>
</div>
<div class="more_info" toggle_block="block1">
    <a href="#" class="btn btn_gray btn_min_width" toggle="block1">Информация о компании</a>
</div>

<select name="contracts_list" class="select_big" <?=(empty($contracts) ? 'disabled' :'')?>>
    <?if(empty($contracts)){?>
        <option>Нет договоров</option>
    <?}else{
    foreach($contracts as $contract){?>
        <option value="<?=$contract['CONTRACT_ID']?>">Договор: <?=$contract['CONTRACT_NAME']?> от <?=$contract['DATE_BEGIN']?> <?if($contract['DATE_END'] != '31.12.2099'){?>до <?=$contract['DATE_END']?><?}?></option>
    <?}}?>
</select>

&nbsp;&nbsp;&nbsp;<span class="btn">+ Создать договор</span>

<div class="block block_margin f18">
    <span class="gray">Текущий баланс:</span> <b>1 022 045,05 &#x20bd;</b>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="gray">Оборот за текущий перид:</span> <b>55 522,56 л.</b>
</div>

<div class="tabs_block">
    <div class="tabs">
        <a href="#" class="tab active"><i class="icon-contract"></i> Договор</a><a href="#" class="tab"><i class="icon-cards"></i> Карты</a><a href="#" class="tab"><i class="icon-account"></i> Счет</a><a href="#" class="tab"><i class="icon-reports"></i> Отчеты</a>
    </div>
    <div class="tabs_content tabs_content_no_padding">
        <div class="tc_top_line">
            <span toggle_block="block2">0676К/14 от 01.01.2014 г. до 22.02.2015 г. &nbsp; <span class="label label_success">Статус</span></span>
                        <span toggle_block="block2" class="dn gray">
                            <input type="text" value="0676К/14" class="input_big">
                            от
                            <input type="text" value="01.01.2014" class="input_big datepicker" readonly>
                            до
                            <input type="text" value="22.02.2015" class="input_big datepicker" readonly>
                            <select class="select_big"><option>1</option></select>
                        </span>

            <div class="fr" toggle_block="block2"><button class="btn" toggle="block2"><i class="icon-pen"></i> Редактировать</button></div>
            <div class="fr dn" toggle_block="block2">
                <button class="btn btn_green"><i class="icon-ok"></i> Сохранить</button>
                <button class="btn btn_red" toggle="block2"><i class="icon-cancel"></i> Отменить</button>
            </div>
        </div>
        <div class="as_table">
            <div class="col">
                <b class="f18">Оплата:</b>
                <table>
                    <tr>
                        <td class="gray right" width="160">Схема оплаты:</td>
                        <td>
                            <span toggle_block="block2">Порог отключения</span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Переодичность выставления счетов:</td>
                        <td>
                            <span toggle_block="block2">Каждый месяц</span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Валюта:</td>
                        <td>
                            <span toggle_block="block2">Российский Рубль – ₽</span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                </table>
                <br>
                <b class="f18">Ограничения по счету:</b>
                <table>
                    <tr>
                        <td class="gray right" width="160">Блокировка:</td>
                        <td>
                            <span toggle_block="block2">1000</span>
                            <span toggle_block="block2" class="dn"><input type="text"></span>
                            ₽
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Пени:</td>
                        <td>
                            <span toggle_block="block2">18%</span>
                            <span toggle_block="block2" class="dn"><input type="text"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Овердрафт:</td>
                        <td>
                            <span toggle_block="block2">500</span>
                            <span toggle_block="block2" class="dn"><input type="text"></span>
                            ₽
                        </td>
                    </tr>
                </table>
            </div><div class="col line_inner">
                <b class="f18">Тарификация</b>
                <table>
                    <tr>
                        <td class="gray right">Online тариф:</td>
                        <td>
                            <span toggle_block="block2">Заграница +2.0% | РН-Аи +1.5%</span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Offline тариф:</td>
                        <td>
                            <span toggle_block="block2">[85] 832 -3.0% | Несте -1.5%</span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var clientId = <?=$client['CLIENT_ID']?>;
</script>