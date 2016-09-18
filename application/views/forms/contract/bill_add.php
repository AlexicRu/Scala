<table class="table_form form_client_add_bill">
    <tr>
        <td class="gray right" width="170">Сумма:</td>
        <td>
            <input type="text" name="client_add_bill_name" class="input_big">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse btn_client_add_bill_go">Выставить счет</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.btn_client_add_bill_go').on('click', function(){
            var params = {
                contract_id:    $('[name=contracts_list]').val(),
                sum:            $('[name=client_add_bill_name]').val()
            };

            if(params.sum == '' || params.sum <= 0){
                message(0, 'Введите корректную сумму');
                return false;
            }

            window.location.href = '/clients/add_bill?' + $.param(params);
            $.fancybox.close();
        });
    });
</script>