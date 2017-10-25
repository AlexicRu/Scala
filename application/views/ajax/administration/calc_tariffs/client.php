<fieldset>
    <legend>Клиент</legend>
    <span class="btn btn_small btn_icon btn_red ts_remove" onclick="calcTariffsDelRow($(this))"><i class="icon-cancel"></i></span>

    <table>
        <tr>
            <td rowspan="2" class="gray right">Договор:</td>
            <td rowspan="2">
                <?=Common::buildFormField('contract_choose_single', 'client_' . $iteration, false, [
                    'depend_field_name' => 'contract_' . $iteration
                ])?>
            </td>
            <td class="gray right">Текущий тариф:</td>
            <td>
                Текущий тариф
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
                <input type="text" name="date_start_<?=$iteration?>" class="datepicker" readonly>
                -
                <input type="text" name="date_end_<?=$iteration?>" class="datepicker" readonly>
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
</script>