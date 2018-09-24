<fieldset>
    <legend>Клиент</legend>
    <span class="btn btn_small btn_icon btn_red ts_remove" onclick="deleteRow($(this))"><i class="icon-cancel"></i></span>

    <table>
        <tr>
            <td rowspan="2" class="gray right">Договор:</td>
            <td rowspan="2">
                <?=Form::buildField('contract_choose_single', 'contract_' . $iteration, false, [
                    'depend_on' => ['name' => 'client_' . $iteration],
                    'onSelect' => 'findTariffByContract'
                ])?>
            </td>
            <td class="gray right">Текущий тариф:</td>
            <td>
                <span class="current_tariff"></span>
            </td>
            <td rowspan="2" class="btns">
                <span class="calc_tariffs_client_ok btn btn_small btn_green btn_reverse dn"><i class="icon-ok"></i> Ok</span>
                <span class="calc_tariffs_client_go btn btn_small btn_reverse dn">... Расчет</span>
                <span class="calc_tariffs_client_error btn btn_small btn_red btn_reverse dn">Ошибка</span>
            </td>
        </tr>
        <tr>
            <td class="gray right">Период:</td>
            <td>
                <input type="text" name="date_start_<?=$iteration?>" class="datepicker" readonly value="<?=date('01.m.Y')?>">
                -
                <input type="text" name="date_end_<?=$iteration?>" class="datepicker" readonly value="<?=date('d.m.Y')?>">
            </td>
        </tr>
    </table>
</fieldset>

<script>
    $(function () {
        $('.datepicker').each(function () {
            renderDatePicker($(this));
        });
    });

    function findTariffByContract(contractId)
    {
        var row = $('[value=' + contractId + ']').closest('fieldset');

        $.post('/administration/get-tariff-by-contract', {contract_id: contractId}, function (data) {
            if (data.success) {
                row.find('.current_tariff').text(data.data.name);
            } else {
                message(0, 'Тариф не найден. Договор: ' +
                    $('[value=' + contractId + ']').closest('.form_field').find('[type=text]').val()
                );
            }
        });
    }
</script>