<div class="block block_margin f18">
    <span class="gray">Текущий баланс:</span> <b><?=number_format($balance['BALANCE'], 2, ',', ' ')?> &#x20bd;</b>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <span class="gray">Оборот за текущий перид:</span> <b>55 522,56 л.</b>
</div>
<?print_r($contract)?>
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
                            <span toggle_block="block2"><?=Model_Contract::$paymentSchemes[$contract['scheme']]?></span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Переодичность выставления счетов:</td>
                        <td>
                            <?
                                if($contract['INVOICE_PERIOD_TYPE'] == Model_Contract::INVOICE_PERIOD_TYPE_DAY){
                                    $period = Text::plural($contract['INVOICE_PERIOD_VALUE'], ['день', 'дня', 'дней']);
                                }else{
                                    $period = Text::plural($contract['INVOICE_PERIOD_VALUE'], ['месяц', 'месяца', 'месяцев']);
                                }
                            ?>
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
                            <span toggle_block="block2"><?=(!empty($contract['AUTOBLOCK_FLAG']) ? $contract['AUTOBLOCK_LIMIT'] : '<span class="gray">Отсутствует</span>')?></span>
                            <span toggle_block="block2" class="dn"><input type="text"></span>
                            ₽
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Пени:</td>
                        <td>
                            <span toggle_block="block2"><?=(!empty($contract['PENALTIES_FLAG']) ? $contract['PENALTIES'] : '<span class="gray">Отсутствует</span>')?></span>
                            <span toggle_block="block2" class="dn"><input type="text"></span>
                            %
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Овердрафт:</td>
                        <td>
                            <span toggle_block="block2"><?=$contract['OVERDRAFT']?></span>
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
                            <span toggle_block="block2"><?=$contract['TARIF_NAME_ONLINE']?></span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="gray right">Offline тариф:</td>
                        <td>
                            <span toggle_block="block2"><?=$contract['TARIF_NAME_OFFLINE']?></span>
                            <span toggle_block="block2" class="dn"><select><option>1</option></select></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>