<form method="post" onsubmit="return checkFormCustomerSettings($(this));">
    <input type="hidden" name="form_type" value="settings">
    <div class="as_table">
        <div class="col">
            <table class="table_form">
                <tr>
                    <td class="gray right" width="170">Имя:</td>
                    <td>
                        <input type="text" name="customer_settings_name" class="input_big" value="<?=$user['MANAGER_NAME']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right" width="170">Фамилия:</td>
                    <td>
                        <input type="text" name="customer_settings_surname" class="input_big" value="<?=$user['MANAGER_SURNAME']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right" width="170">Отчество:</td>
                    <td>
                        <input type="text" name="customer_settings_middlename" class="input_big" value="<?=$user['MANAGER_MIDDLENAME']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right" width="170">E-mail:</td>
                    <td>
                        <input type="text" name="customer_settings_email" class="input_big" value="<?=$user['EMAIL']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right" width="170">Телефон:</td>
                    <td>
                        <input type="text" name="customer_settings_phone" class="input_big" value="<?=$user['CELLPHONE']?>">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button class="btn btn_green btn_reverse btn_customer_settings_go"><i class="icon-ok"></i> Сохранить</button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col line_inner">
            <b class="f18">Смена пароля</b>
            <table class="table_form">
                <tr class="form_attention">
                    <td class="gray right" width="170">Пароль:</td>
                    <td>
                        <input type="password" name="customer_settings_password" class="input_big" <?=($user['MANAGER_ID'] == Access::USER_TEST ? 'readonly' : '')?>>
                    </td>
                </tr>
                <tr class="form_attention">
                    <td class="gray right" width="170">Пароль еще раз:</td>
                    <td>
                        <input type="password" name="customer_settings_password_again" class="input_big" <?=($user['MANAGER_ID'] == Access::USER_TEST ? 'readonly' : '')?>>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</form>

<script>
    function checkFormCustomerSettings(form)
    {
        var pass = $('[name=customer_settings_password]', form).val();
        var passAgain = $('[name=customer_settings_password_again]', form).val();

        if(pass != passAgain){
            message(0, 'Пароли не совпадают');
            return false;
        }

        return true;
    }
</script>