<table class="table_form form_add_client">
    <tr>
        <td class="gray right" width="170">Название компании:</td>
        <td>
            <input type="text" name="add_client_name" class="input_big">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="submitForm($(this), addClientGo)">+ Добавить клиента</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    function addClientGo(btn) {
        var params = {
            name: $('[name=add_client_name]').val()
        };

        if(params.name == ''){
            message(0, 'Введите название компании');
            endSubmitForm();
            return false;
        }

        $.post('/clients/client-add', {params:params}, function(data){
            if(data.success){
                message(1, 'Клиент успешно добавлен');
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }else{
                message(0, 'Ошибка добавления клиента');
                endSubmitForm();
            }
        });
    }
</script>