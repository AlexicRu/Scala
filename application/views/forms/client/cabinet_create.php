<table class="table_form form_cabinet_create">
    <!--tr>
        <td class="gray right" width="170">Логин:</td>
        <td>
            <input type="number" name="cabinet_create_login" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Пароль:</td>
        <td>
            <input type="password" name="cabinet_create_password" class="input_big">
        </td>
    </tr-->
    <tr>
        <td class="gray right" width="170">Email:<br><small><i>куда отправить</i></small></td>
        <td>
            <input type="text" class="input_big" name="cabinet_create_email">
        </td>
    </tr>
    <tr>
        <td class="gray right">Роль:</td>
        <td>
            <select name="cabinet_create_role">
                <?foreach(Access::$clientRoles as $roleId => $role){?>
                    <option value="<?=$roleId?>"><?=$role?></option>
                <?}?>
            </select>
        </td>
    </tr>
    <!--tr>
        <td class="gray right" width="170">Отправить клиенту данные:</td><td>
            <input type="checkbox" class="input_big" name="cabinet_create_send">
        </td>
    </tr-->
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse btn_cabinet_create_go">+ Создать</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.btn_cabinet_create_go').on('click', function(){
            var params = {
                client_id:      clientId ,
                email_to:       $('[name=cabinet_create_email]').val(),
                role:           $('[name=cabinet_create_role]').val(),
            };

            if(params.email_to == ''){
                message(0, 'Введите email');
                return false;
            }

            $.post('/clients/cabinet_create', {params:params}, function(data){
                if(data.success){
                    message(1, 'Личный кабинет успешно создан');
                    $.fancybox.close();
                }else{
                    message(0, data.data ? data.data : 'Ошибка создания личного кабинета');
                }
            });
        });
    });
</script>