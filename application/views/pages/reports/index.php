<h1>Отчеты</h1>

<div class="tabs_vertical_block tabs_switcher tabs_reports">
    <div class="tabs_v">
        <div class="tab_v active" tab="1"><div>
            <a href="#"><span class="icon-dailes f20"></span> Поставщики</a>
        </div></div>
        <div class="tab_v" tab="4"><div>
            <a href="#"><span class="icon-dailes f20"></span> Клиентские</a>
        </div></div>
        <div class="tab_v" tab="2"><div>
            <a href="#"><span class="icon-analytics f20"></span> Аналитические</a>
        </div></div>
        <div class="tab_v" tab="3"><div>
            <a href="#"><span class="icon-summary f20"></span> Прочие</a>
        </div></div>
    </div>
    <div class="tabs_v_content">
        <div class="tab_v_content active" tab_content="1">
            <table>
                <tr>
                    <td class="gray right" width="200">Шаблон:</td>
                    <td>
                        <select class="select_big">
                            <option>шаблон1</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Период:</td>
                    <td>
                        <input type="text" name="fr_date_start" class="input_big datepicker" readonly value="<?=date('01.m.Y')?>"> -
                        <input type="text" name="fr_date_end" class="input_big datepicker" readonly value="<?=date('d.m.Y')?>">
                    </td>
                </tr>
                <tr>
                    <td class="td_title">Дополнительные параметры:</td>
                    <td>
                        <table>
                            <tr>
                                <td class="td_title" width="100">Клиент:</td>
                                <td>
                                    <input type="text" class="input_big"> <button class="btn btn_icon btn_small"><i class="icon-find"></i></button>

                                    <div class="selected_clients_list">
                                        <div class="scl_item" client_id="4">Тестовый клиент<span class="scli_close" onclick="uncheckFoundClient($(this))">×</span></div>
                                        <div class="scl_item" client_id="82">Первая топливная компания<span class="scli_close" onclick="uncheckFoundClient($(this))">×</span></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="td_title"></td>
                                <td>
                                    <label><input type="checkbox"> Указывать стоимость ТО</label>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Формат файла:</td>
                    <td>
                        <span class="btn btn_toggle active" format="xls"><i class="icon-exel1 icon_big"></i> XLS</span>
                        <span class="btn btn_toggle" format="pdf"><i class="icon-pdf icon_big"></i> PDF</span>
                        <span class="btn btn_toggle"><i class="icon-exel2 icon_big"></i> CXV</span>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <br>
                        <span class="btn"><i class="icon-download"></i> Сформировать</span>
                        <span class="btn btn_orange"><i class="icon-notifications"></i> На почту</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="tab_v_content" tab_content="2">2</div>
        <div class="tab_v_content" tab_content="3">3</div>
        <div class="tab_v_content" tab_content="4">4</div>
    </div>
</div>