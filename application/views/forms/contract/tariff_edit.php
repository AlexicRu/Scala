<table class="table_form form_contract_tariff_edit">
    <tr>
        <td class="gray right" width="170">Тариф:</td>
        <td>
            <?=Form::buildField('contract_tariffs', 'TARIF_OFFLINE', $tariffId)?>
        </td>
    </tr>
    <tr>
        <td class="gray right">Дата начала действия тарифа:</td>
        <td>
            <input type="text" class="datepicker" readonly name="contract_tariff_edit_date">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="submitForm($(this), contractTariffEditGo)"><i class="icon-ok"></i> Сохранить</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function () {
        $('[name=card_edit_date]').each(function () {
            renderDatePicker($(this));
        });
    });

    function contractTariffEditGo(t)
    {
        var form = t.closest('.form_contract_tariff_edit');
        var params = {
            contract_id : $('[name=contracts_list]').val(),
            tariff_id : getComboboxValue($('[name=TARIF_OFFLINE]', form)),
            date_from : $('[name=contract_tariff_edit_date]', form).val(),
        };

        if(params.date == false){
            message(0, 'Заполните дату');
            return;
        }

        $.post('/clients/contract-tariff-edit', params, function (data) {
            if (data.success) {
                message(1, 'Тариф успешно обновлен');

                loadContract('contract');
            } else {
                message(0, 'Ошибка обновления тарифа');
            }
        });
    }
</script>