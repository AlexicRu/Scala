<div class="tabs_vertical_block tabs_switcher">
    <div class="tabs_v">
        <div class="tab_v active" tab="1"><div>
                <a href="#"><span class="icon-dailes f20"></span> Повседневные</a>
            </div></div>
        <?/*div class="tab_v" tab="2"><div>
                <a href="#"><span class="icon-summary f20"></span> Итоговые</a>
            </div></div>
        <div class="tab_v" tab="3"><div>
                <a href="#"><span class="icon-analytics f20"></span> Аналитические</a>
            </div></div*/?>
    </div>
    <div class="tabs_v_content">
        <div class="tab_v_content active" tab_content="1">
            <table form_report="daily">
                <tr>
                    <td class="gray right" width="160">Период:</td>
                    <td>
                        <input type="text" name="fr_daily_date_start" class="input_big datepicker" readonly value="<?=date('01.m.Y')?>"> -
                        <input type="text" name="fr_daily_date_end" class="input_big datepicker" readonly value="<?=date('d.m.Y')?>">
                    </td>
                </tr>
                <?/*tr>
                    <td class="gray right">Карта:</td>
                    <td>
                        <select class="select_big">
                            <option>Карта #1</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Услуга:</td>
                    <td>
                        <select class="select_big">
                            <option>Заправка</option>
                        </select>
                    </td>
                </tr*/?>
                <tr>
                    <td class="gray right">Формат файла:</td>
                    <td>
                        <?/*span class="btn btn_toggle" format="pdf"><i class="icon-pdf icon_big"></i> PDF</span*/?>
                        <span class="btn btn_toggle active" format="xls"><i class="icon-exel1 icon_big"></i> XLS</span>
                        <?/*span class="btn btn_toggle"><i class="icon-exel2 icon_big"></i> CXV</span*/?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <span class="btn btn_get_daily_report"><i class="icon-download"></i> Скачать</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="tab_v_content" tab_content="2">
            <table>
                <tbody><tr>
                    <td class="gray right" width="160">Месяц:</td>
                    <td>
                        <select class="select_big">
                            <option>Ноябрь</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Год:</td>
                    <td>
                        <span class="btn btn_toggle btn_small">2010</span>
                        <span class="btn btn_toggle btn_small">2011</span>
                        <span class="btn btn_toggle btn_small">2012</span>
                        <span class="btn btn_toggle btn_small">2013</span>
                        <span class="btn btn_toggle btn_small">2014</span>
                        <span class="btn btn_toggle btn_small">2015</span>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Формат файла:</td>
                    <td>
                        <span class="btn btn_toggle"><i class="icon-pdf icon_big"></i> PDF</span>
                        <span class="btn btn_toggle"><i class="icon-exel1 icon_big"></i> XLS</span>
                        <span class="btn btn_toggle"><i class="icon-exel2 icon_big"></i> CXV</span>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <span class="btn"><i class="icon-download"></i> Скачать</span>
                    </td>
                </tr>
                </tbody></table>
        </div>
        <div class="tab_v_content" tab_content="3">
            <table>
                <tbody><tr>
                    <td class="gray right" width="160">Форма отчета:</td>
                    <td>
                        <select class="select_big">
                            <option>Отчет таблицей</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Формат файла:</td>
                    <td>
                        <span class="btn btn_toggle"><i class="icon-pdf icon_big"></i> PDF</span>
                        <span class="btn btn_toggle"><i class="icon-exel1 icon_big"></i> XLS</span>
                        <span class="btn btn_toggle"><i class="icon-exel2 icon_big"></i> CXV</span>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <span class="btn"><i class="icon-download"></i> Скачать</span>
                    </td>
                </tr>
                </tbody></table>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.btn_get_daily_report').on('click', function () {

            var t = $(this);
            var form = t.closest('[form_report]');

            var params = {
                contract_id:    $('[name=contracts_list]').val(),
                type:           form.attr('form_report'),
                format:         form.find('[format].active').attr('format'),
                date_start:     form.find('[name=fr_daily_date_start]').val(),
                date_end:       form.find('[name=fr_daily_date_end]').val()
            };

            window.location.href = '/reports/generate?' + $.param(params);
        });
    });
</script>