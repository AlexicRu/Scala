<style>
    .calc_tariffs_client_block:not(:empty){
        margin-bottom: 30px;
    }
</style>

<h1>Расчет тарифов</h1>

<div class="tabs_block tabs_switcher tabs_administration_calc_tariffs">
    <div class="tabs">
        <span tab="client" class="tab active">Тариф клиента</span><span tab="close" class="tab">Закрытие периода</span><span tab="queue" class="tab" onclick="loadQueueCalc()">Очередь расчета</span>
    </div>
    <div class="tabs_content">
        <div tab_content="client" class="tab_content active">
            <div class="calc_tariffs_client_block t_sections_list">

            </div>

            <div class="btn" onclick="addCalcTariffsClient()">+ Добавить клиента</div>

            <div class="row_btns">
                <div class="btn btn_green" onclick="calcTariffsGo()"><i class="icon-ok"></i> Рассчитать</div>
            </div>
            <br>
            <i class="gray">
                * - дата начала и дата окончания расчета тарифа не старше чем 3 месяца от текущей даты. Для расчета тарифа за более ранний период сделайте запрос через <a href="/support">Поддержку</a>
            </i>
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
        <div tab_content="queue" class="tab_content">
            <div class="calc_queue"></div>
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

    function loadQueueCalc()
    {
        var block = $('.calc_queue');

        block.addClass(CLASS_LOADING);

        $.post('/administration/load-calc-queue', {}, function (data) {
            block.removeClass(CLASS_LOADING);

            block.html(data);
        })
    }

    var contractsInAction = [];

    function calcTariffsGo()
    {
        var block = $('.calc_tariffs_client_block');

        var flEmpty = true;

        block.find('fieldset').each(function () {
            var t = $(this);

            if (t.find('.btns .btn:visible').length == 0) {

                var contractId = getComboboxValue(t.find('[name^=contract_]'));

                if (contractId) {
                    if (contractsInAction.indexOf(contractId) == -1) {
                        contractsInAction.push(contractId);
                        flEmpty = false;
                        calcTariff(t);
                    } else {
                        message(0, 'По одному договору нельзя запустить несколько расчетов.<br>Договор: '
                            + $('[value=' + contractId + ']').closest('.form_field').find('[type=text]').val()
                        );
                        t.find('.btns .calc_tariffs_client_error').show();
                    }
                }
            }
            block.find('input[type=text]').prop('disabled', true);
        });

        if (flEmpty) {
            message(0, 'Нет данных для расчета');
        }

        setTimeout(function () {
            loadQueueCalc();
        }, 1000);
    }

    function calcTariff(block)
    {
        var params = {
            client_id:   getComboboxValue(block.find('[name^=client_]')),
            contract_id: getComboboxValue(block.find('[name^=contract_]')),
            start:       block.find('[name^=date_start_]').val(),
            end:         block.find('[name^=date_end_]').val(),
        };

        if (!params.contract_id) {
            message(0, 'Во всех клиентах должны быть выбраны договоры');
            return false;
        }

        if (!params.start || !params.end) {
            message(0, 'Для договора: "'+ $('[value='+ params.contract_id +']').closest('.form_field').find('[type=text]').val() +'" необходимо заполнить даты');
            return false;
        }

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