<?
if(empty($manager)){
    $manager = $user;
}
if(empty($width)){
    $width = 170;
}
if(!isset($reload)){
    $reload = true;
}
if(empty($changeRole)){
    $changeRole = false;
}
?>
<form method="post" onsubmit="return checkFormManagerSettings($(this));">
    <input type="hidden" name="form_type" value="settings">
    <div class="as_table">
        <div class="col">
            <table class="table_form">
                <tr>
                    <td class="gray right" width="<?=$width?>">Имя:</td>
                    <td>
                        <input type="text" name="manager_settings_name" class="input_big" value="<?=$manager['MANAGER_NAME']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Фамилия:</td>
                    <td>
                        <input type="text" name="manager_settings_surname" class="input_big" value="<?=$manager['MANAGER_SURNAME']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Отчество:</td>
                    <td>
                        <input type="text" name="manager_settings_middlename" class="input_big" value="<?=$manager['MANAGER_MIDDLENAME']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right">E-mail:</td>
                    <td>
                        <input type="text" name="manager_settings_email" class="input_big" value="<?=$manager['EMAIL']?>">
                    </td>
                </tr>
                <tr>
                    <td class="gray right">Телефон:</td>
                    <td>
                        <input type="text" name="manager_settings_phone" class="input_big" value="<?=$manager['CELLPHONE']?>">
                    </td>
                </tr>
                <?if($changeRole){?>
                    <tr>
                        <td class="gray right">Роль:</td>
                        <td>
                            <select name="manager_settings_role" class="select_big">
                                <?foreach(Access::$roles as $role => $name){?>
                                    <option value="<?=$role?>" <?if($role == $manager['ROLE_ID']){?>selected<?}?>><?=$name?></option>
                                <?}?>
                            </select>
                        </td>
                    </tr>
                <?}?>
                <tr>
                    <td></td>
                    <td>
                        <button class="btn btn_green btn_reverse btn_manager_settings_go"><i class="icon-ok"></i> Сохранить</button>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col line_inner">
            <b class="f18">Смена пароля</b>
            <table class="table_form">
                <tr class="form_attention">
                    <td class="gray right" width="<?=$width?>">Логин:</td>
                    <td>
                        <?=$manager['LOGIN']?>
                    </td>
                </tr>
                <tr class="form_attention">
                    <td class="gray right">Пароль:</td>
                    <td>
                        <input type="password" name="manager_settings_password" class="input_big" <?=($manager['MANAGER_ID'] == Access::USER_TEST ? 'readonly' : '')?>>
                    </td>
                </tr>
                <tr class="form_attention">
                    <td class="gray right">Пароль еще раз:</td>
                    <td>
                        <input type="password" name="manager_settings_password_again" class="input_big" <?=($manager['MANAGER_ID'] == Access::USER_TEST ? 'readonly' : '')?>>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</form>

<script>
    function checkFormManagerSettings(form)
    {
        var pass = $('[name=manager_settings_password]', form).val();
        var passAgain = $('[name=manager_settings_password_again]', form).val();

        if(pass != passAgain){
            message(0, 'Пароли не совпадают');
            return false;
        }

        $.post('/managers/settings', form.serialize(), function (data) {
           if(data.success){
               <?if($reload){?>
               window.location.reload();
               <?}?>

               message(1, 'Данные обновлены');
           }else{
               message(0, 'Ошибка обновления');
           }
        });

        return false;
    }
</script>