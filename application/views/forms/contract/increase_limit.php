<table class="table_form form_contract_increase_limit">
    <tr>
        <td class="gray right" width="170">Количество:</td>
        <td>
            <input type="text" name="contract_increase_limit_name" class="input_big">

            <span class="icon-question" title="Значение со знаком минус уменьшит лимит"></span>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="contractIncreaseLimit()">Сохранить</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    function contractIncreaseLimit()
    {
        var params = {
            contract_id:    increaseLimitContractId,
            group_id:       increaseLimitGroupId,
            amount:         $('[name=contract_increase_limit_name]').val()
        };

        if(params.amount == ''){
            message(0, 'Введите корректное количество');
            return false;
        }

        $.post('/clients/contract_increase_limit', params, function(data){
            if (data.success) {
                message(1, 'Лимит успешно изменен');
                loadContract('account');
            } else {
                message(0, 'Ошибка изменения лимита');
            }
        });
    }
</script>