<fieldset class="form_client_add_bill_product">
    <legend>Товар</legend>
    <span class="btn btn_small btn_icon btn_red ts_remove" onclick="addBillDeleteRow($(this))"><i class="icon-cancel"></i></span>

    <table>
        <tr>
            <td>
                Вид топлива:<br>
                <?=Common::buildFormField('service_choose_single', 'add_bill_product_service_' . $iteration)?>
            </td>
            <td>
                Кол-во:<br>
                <input type="text" name="add_bill_product_cnt_<?$iteration?>" onkeyup="calcRowSumm($(this))">
            </td>
            <td>
                Цена:<br>
                <input type="text" name="add_bill_product_price_<?$iteration?>" onkeyup="calcRowSumm($(this))">
            </td>
            <td>
                Сумма:<br>
                <input type="text" name="add_bill_product_summ_<?$iteration?>" disabled>
            </td>
        </tr>
    </table>
</fieldset>