<style>
    .client_contract_elem{
        display: inline-block; vertical-align: top; margin: 0 10px 30px 0; position: relative; padding: 0 40px 0 0;
    }
    .client_contract_elem .ts_remove{
        top:0; right: 0;
    }
</style>
<table class="table_form export_1c">
    <tr>
        <td class="gray right" width="150">Период:</td>
        <td>
            <input type="text" name="date_from" class="input_big input_medium datepicker" readonly>
            -
            <input type="text" name="date_to" class="input_big input_medium datepicker" readonly>
        </td>
    </tr>
    <tr>
        <td class="gray right">
            Клиент:<br>
            Договор:
        </td>
        <td>
            <div class="client_contracts_list"></div>
            <button class="btn btn_small" onclick="renderNewClientForm()">+ Добавить клиента</button>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <button class="btn btn_green btn_reverse btn_manager_settings_go" onclick="export1c()">Выгрузить</button>
        </td>
    </tr>
</table>

<script>
    $(function () {
        renderNewClientForm();
    });

    function renderNewClientForm()
    {
        var block = $('.client_contracts_list');

        var iteration = $('.client_contract_elem', block).length + 1;

        $.get('/control/client-contract-form?iteration=' + iteration, function (data) {
            block.append(data);
        });
    }

    function export1c()
    {
        var block = $('.export_1c');

        var params = {
            date_from: $('[name=date_from]', block).val(),
            date_to: $('[name=date_to]', block).val(),
            contracts: []
        };

        if (!params.date_from || !params.date_to) {
            message(0, 'Выберите даты');
            return;
        }

        $('.client_contracts_list .client_contract_elem', block).each(function () {
            var t = $(this);
            var field = $('[name^=client_contract]', t);

            var value = getComboboxValue(field);

            params.contracts.push(value);
        });

        window.open('/control/1c-export?' + $.param(params));
    }
</script>