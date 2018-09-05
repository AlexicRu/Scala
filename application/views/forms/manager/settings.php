<?
$isEdit = true;
if(empty($manager)){
    $manager = $user;
    $isEdit = false;
}
if(!isset($reload)){
    $reload = true;
}
?>
<form method="post" onsubmit="return checkFormManagerSettings($(this));">
    <?if($isEdit){?>
        <input type="hidden" name="manager_settings_id" value="<?=$manager['MANAGER_ID']?>">
    <?}?>
    <div class="as_table">
        <div class="col">
            <table class="table_form">
                <tr>
                    <td class="gray right" width="170">Имя:</td>
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
                <?if (Access::allow('change_phone_note')) {?>
                <tr>
                    <td class="gray right">Телефон для оповещений:</td>
                    <td>
                        <input type="text" name="manager_settings_phone_note" class="input_big"  value="<?=$manager['PHONE_FOR_SMS']?>">
                    </td>
                </tr>
                <?}?>
                <?if(!empty($changeRole)){?>
                    <tr>
                        <td class="gray right">Роль:</td>
                        <td>
                            <select name="manager_settings_role" class="select_big">
                                <?foreach(Access::getAvailableRoles() as $role => $name){?>
                                    <option value="<?=$role?>" <?if($role == $manager['ROLE_ID']){?>selected<?}?>><?=$name?></option>
                                <?}?>
                            </select>
                        </td>
                    </tr>
                <?}?>
                <?if (Access::allow('change_manager_settings_limit') && in_array($manager['ROLE_ID'], array_keys(Access::$clientRoles))) {?>
                    <tr>
                        <td></td>
                        <td>
                            <label>
                                <input type="checkbox" name="manager_settings_limit" <?if ($manager['LIMIT_RESTRICTION'] == 1) {?>checked<?}?>> Ограничение в 1000 литров и 30000 рублей на лимит
                            </label>
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
                    <td class="gray right" width="170">Логин:</td>
                    <td>
                        <?if (Access::allow('clients_edit-login')) {?>
                            <div toggle_block="edit_login">
                                <span class="login_value"><?=$manager['LOGIN']?></span>
                                <span class="btn btn_small" toggle="edit_login"><i class="icon icon-pen"></i></span>
                            </div>
                            <div toggle_block="edit_login" style="display: none">
                                <input type="text" value="<?=$manager['LOGIN']?>" name="edit_login" class="input_big input_mini">
                                <span class="btn btn_small btn_green" onclick="editLogin($(this));"><i class="icon icon-ok"></i></span>
                                <span class="btn btn_small btn_red" toggle="edit_login"><i class="icon icon-cancel"></i></span>
                            </div>
                        <?} else {?>
                            <?=$manager['LOGIN']?>
                        <?}?>
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
    $(function () {
        $("[name=manager_settings_phone], [name=manager_settings_phone_note]").each(function () {
            renderPhoneInput($(this));
        });
        renderCheckbox($('[name=manager_settings_limit]'));
    });

    function checkFormManagerSettings(form)
    {
        var pass = $('[name=manager_settings_password]', form).val();
        var passAgain = $('[name=manager_settings_password_again]', form).val();

        if(pass != passAgain){
            message(0, 'Пароли не совпадают');
            return false;
        }

        var phone = $("[name=manager_settings_phone]");
        var phoneNote = $("[name=manager_settings_phone_note]");

        if (
            phone.intlTelInput('isValidNumber') == false &&
            ('+' + phone.intlTelInput("getSelectedCountryData").dialCode) != phone.intlTelInput('getNumber') &&
            phone.intlTelInput('getNumber') != ''
        ) {
            message(0, 'Некорректный номер телефона');
            return false;
        }

        if (
            phoneNote.intlTelInput('isValidNumber') == false &&
            ('+' + phoneNote.intlTelInput("getSelectedCountryData").dialCode) != phoneNote.intlTelInput('getNumber') &&
            phoneNote.intlTelInput('getNumber') != ''
        ) {
            message(0, 'Некорректный номер телефона для оповещений');
            return false;
        }

        $.post('/managers/settings', form.find(':input[name!="edit_login"]').serialize(), function (data) {
           if(data.success){
               message(1, 'Данные обновлены');

               <?if($reload){?>
               setTimeout(function () {
                   window.location.reload();
               }, 1000);
               <?}?>
           }else{
               var error = 'Ошибка обновления';
               if (data.data) {
                   error = data.data;
               }
               message(0, error);
           }
        });

        return false;
    }

    function editLogin(btn)
    {
        var td = btn.closest('td');
        var txt = td.find('.login_value');
        var input = td.find('[name=edit_login]');
        var form = btn.closest('form');
        var managerId = form.find('[name=manager_settings_id]').val();

        if(input.val() == ''){
            message(0, 'Логин не должен быть пустым');
            return false;
        }

        var params = {
            login: input.val(),
            manager_id: managerId
        };

        $.post('/clients/edit-login', params, function (data) {
            if(data.success){
                message(1, 'Логин обновлен');
                txt.text(data.data.login)
                td.find('[toggle=edit_login]:first').click();
            }else{
                message(0, 'Ошибка. ' + data.data.error);
            }
        });
    }
</script>