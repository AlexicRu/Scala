<style>
    .calc_tariffs_client_block:not(:empty){
        margin-bottom: 30px;
    }
</style>

<h1>Расчет тарифов</h1>

<div class="tabs_block tabs_switcher tabs_administration_calc_tariffs">
    <div class="tabs">
        <span tab="client" class="tab active">Тариф клиента</span><span tab="close" class="tab">Закрытие периода</span>
    </div>
    <div class="tabs_content">
        <div tab_content="client" class="tab_content active">
            <div class="calc_tariffs_client_block t_sections_list">

            </div>

            <div class="btn" onclick="addCalcTariffsClient()">+ Добавить клиента</div>

            <div class="row_btns">
                <div class="btn btn_green" onclick="calcTariffsGo()"><i class="icon-ok"></i> Рассчитать</div>
            </div>
        </div>
        <div tab_content="close" class="tab_content">
            <?
                include __DIR__ . '/calc_tariffs/close.php';
            ?>
            <br>
            <table>
                <tr>
                    <td class="right gray">
                        Закрыть период на дату:
                    </td>
                    <td>
                        <input type="text" name="close_by_day" class="datepicker" readonly>
                    </td>
                    <td>
                        <span class="btn">Запуск</span>
                    </td>
                </tr>
            </table>


        </div>
    </div>
</div>

<script>
    $(function () {
        addCalcTariffsClient();
    });

    function addCalcTariffsClient()
    {
        var block = $('.calc_tariffs_client_block');

        var params = {
            iteration: block.find('fieldset').length + 1
        };

        $.post('/administration/calc-tariffs-render-client', params, function (data) {
            block.append(data)
        });
    }

    function calcTariffsGo()
    {
        var block = $('.calc_tariffs_client_block');

        block.find('fieldset').each(function () {
            calcTariff($(this));
        });
    }

    function calcTariff(block)
    {
        var params = {
            client_id:   getComboboxValue(block.find('[name^=client_]')),
            contract_id: getComboboxValue(block.find('[name^=contract_]')),
            start:       block.find('[name^=date_start_]').val(),
            end:         block.find('[name^=date_end_]').val(),
        };

        block.find('.btns .btn').hide();
        block.find('.btns .calc_tariffs_client_go').show();

        $.post('/administration/calc-tariff', params, function (data) {
            block.find('.btns .btn').hide();
            if (data.success) {
                block.find('.btns .calc_tariffs_client_ok').show();
            } else {
                block.find('.btns .calc_tariffs_client_error').show();
            }
        });
    }
</script>