<table class="table_form form_add_contract_payment">
    <tr>
        <td class="gray right" width="170">Номер:</td>
        <td>
            <input type="number" name="add_contract_payment_num" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Дата платежного поручения:</td>
        <td>
            <input type="text" name="add_contract_payment_date" class="input_big datepicker" readonly maxDate="1" value="<?=date('d.m.Y')?>">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Сумма (<?=Text::RUR?>):</td>
        <td>
            <input type="text" name="add_contract_payment_value" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Комментарий:</td>
        <td>
            <textarea name="add_contract_payment_comment"></textarea>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="submitForm($(this), addContractPaymentGo)">+ Добавить платеж</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function () {
        money($('[name=add_contract_payment_value]'))
    });

    function addContractPaymentGo(btn)
    {
        var params = {
            contract_id:    $('[name=contracts_list]').val(),
            num:            $('[name=add_contract_payment_num]').val(),
            date:           $('[name=add_contract_payment_date]').val(),
            value:          getMoney($('[name=add_contract_payment_value]')),
            comment:        $('[name=add_contract_payment_comment]').val()
        };

        if(params.num == ''){
            message(0, 'Введите номер');
            endSubmitForm();
            return false;
        }

        if(params.date == ''){
            message(0, 'Введите дату');
            endSubmitForm();
            return false;
        }

        if(params.value == ''){
            message(0, 'Введите сумму');
            endSubmitForm();
            return false;
        }

        $.post('/clients/contract-payment-add', {params:params}, function(data){
            if(data.success){
                message(1, data.data);
                loadContract('account');
            }else{
                message(0, data.data);
            }
            endSubmitForm();
        });
    }
</script>