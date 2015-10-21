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
            <span class="btn btn_reverse btn_add_client_go">+ Добавить клиента</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.btn_add_client_go').on('click', function(){
            var params = {
                name: $('[name=add_client_name]').val()
            };

            if(params.name == ''){
                message(0, 'Введите название компании');
                return false;
            }

            $.post('/clients/client_add', {params:params}, function(data){
                if(data.success){
                    message(1, 'Клиент успешно добавлен');
                }else{
                    message(0, 'Ошибка добавления клиента');
                }
            });
        });
    });
</script>