<table class="table_form form_add_agreement">
    <tr>
        <td class="gray right" width="170">Название:</td>
        <td>
            <input type="text" name="add_agreement_name" class="input_big">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="addAgreement($(this))">+ Добавить соглашение</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    function addAgreement(btn)
    {
        var block = btn.closest('.form_add_agreement');

        var params = {
            contract_id:    contractId,
            agreement_name: $('[name=add_agreement_name]').val()
        };

        if(params.add_agreement_name == ''){
            message(0, 'Введите название соглашения');
            return false;
        }

        $.post('/suppliers/agreement-add', {params:params}, function(data){
            if(data.success){
                message(1, 'Соглашение успешно добавлено');
                loadSupplierContract('agreements');
                $.fancybox.close();
            }else{
                message(0, data.data ? data.data : 'Ошибка добавления соглашения');
            }
        });
    }
</script>