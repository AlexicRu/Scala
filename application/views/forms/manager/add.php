<table class="table_form">
    <tr>
        <td class="gray right" width="170">Роль<sup class="required">*</sup>:</td>
        <td>
            <select name="manager_add_role" class="select_big">
                <?foreach(Access::$roles as $role => $name){?>
                    <option value="<?=$role?>"><?=$name?></option>
                <?}?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="gray right">Имя:</td>
        <td>
            <input type="text" name="manager_add_name" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right">Фамилия:</td>
        <td>
            <input type="text" name="manager_add_surname" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right">Отчество:</td>
        <td>
            <input type="text" name="manager_add_middlename" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right">E-mail:</td>
        <td>
            <input type="text" name="manager_add_email" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right">Телефон:</td>
        <td>
            <input type="text" name="manager_add_phone" class="input_big">
        </td>
    </tr>
    <tr class="dark">
        <td class="gray right">Логин<sup class="required">*</sup>:</td>
        <td>
            <input type="text" name="manager_add_login" class="input_big">
        </td>
    </tr>
    <tr class="dark">
        <td class="gray right">Пароль<sup class="required">*</sup>:</td>
        <td>
            <input type="password" name="manager_add_password" class="input_big">
        </td>
    </tr>
    <tr class="dark">
        <td class="gray right">Повторите пароль<sup class="required">*</sup>:</td>
        <td>
            <input type="password" name="manager_add_password_again" class="input_big">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <button class="btn btn_reverse btn_manager_add_go" onclick="managerAdd()"><i class="icon-ok"></i> Добавить</button>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    function managerAdd()
    {
        var params = {
            role:           $('[name=manager_add_role]').val(),
            name:           $('[name=manager_add_name]').val(),
            surname:        $('[name=manager_add_surname]').val(),
            middlename:     $('[name=manager_add_middlename]').val(),
            email:          $('[name=manager_add_email]').val(),
            phone:          $('[name=manager_add_phone]').val(),
            login:          $('[name=manager_add_login]').val(),
            password:       $('[name=manager_add_password]').val(),
            password_again: $('[name=manager_add_password_again]').val(),
        };

        if(params.login == '' || params.password == ''){
            message(0, 'Заполните логин и пароль');
            return false;
        }

        if(params.password != params.password_again){
            message(0, 'Пароли не сопадают');
            return false;
        }

        $.post('/managers/add_manager', {params:params}, function (data) {
            if(data.success){
                message(1, 'Менеджер успешно добавлен');
                $.fancybox.close();
            }else{
                message(0, 'Ошибка добавления менеджера');
            }
        });
    }
</script>