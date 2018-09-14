<table class="table_form">
    <tr>
        <td class="gray right" width="170">Телефон для оповещений:</td>
        <td>
            <input type="text" name="manager_settings_phone_note" class="input_big" value="<?=$manager['PHONE_FOR_INFORM']?>">
        </td>
    </tr>
    <tr>
        <td class="gray right">
            <button class="btn btn_small btn_reverse manager_settings_confirm_code_btn" onclick="getSmsConfirmCode($(this))">
                Получить код
            </button>
        </td>
        <td>
            <input type="text" name="manager_settings_confirm_code" class="input_mini">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn" onclick="doAddInform($(this))">
                Подключить
            </span>
        </td>
    </tr>
</table>

<br>

<i class="gray">
    Запросить новый код можно через (секунды): <b class="sms_code_renew">0</b><br>
    Время жизни кода (секунды): <b class="sms_code_lifetime">0</b>
</i>

<script>
    $(function () {
        $("[name=manager_settings_phone_note]").each(function () {
            renderPhoneInput($(this));
        });
    });

    function doAddInform(btn)
    {
        var phoneNote = $("[name=manager_settings_phone_note]");
        var confirmCode = $("[name=manager_settings_confirm_code]").val();

        if (phoneNote.intlTelInput('isValidNumber') == false) {
            message(0, 'Некорректный номер телефона для оповещений');
            return false;
        }

        if (!confirmCode || confirmCode.length != 4) {
            message(0, 'Некорректный код подтверждения');
            return false;
        }

        var params = {
            phone: phoneNote.intlTelInput('getNumber'),
            code: confirmCode,
        };

        $.post('/inform/enable-inform', params, function (data) {
            if (data.success) {
                message(1, 'Информирование успешно подключено');
                $.fancybox.close();

                var form = $('.manager_settings_form:visible');

                $('.manager_settings_inform > div', form).toggle();
            } else {
                var error = '';

                if (data.data) {
                    error = data.data;
                }

                message(0, 'Ошибка подключение информирования. ' + error);
            }
        });
    }

    var SMSCodeRenew = 0;
    var SMSCodeLifetime = 0;
    var i, i2;
    function getSmsConfirmCode(btn)
    {
        if (SMSCodeRenew != 0) {
            return false;
        }

        var phoneNote = $("[name=manager_settings_phone_note]");

        if (phoneNote.intlTelInput('isValidNumber') == false) {
            message(0, 'Некорректный номер телефона для оповещений');
            return false;
        }

        $.post('/inform/send-sms-confirm-code', {phone: phoneNote.intlTelInput('getNumber')}, function (data) {
            if (data.success) {
                message(1, 'СМС с кодом отправлено');

                clearInterval(i);
                clearInterval(i2);

                SMSCodeRenew = data.data.renew;
                SMSCodeLifetime = data.data.lifetime;
                $('.sms_code_renew').text(SMSCodeRenew);
                $('.sms_code_lifetime').text(SMSCodeLifetime);
                btn.prop('disabled', true);

                i = setInterval(function () {
                    SMSCodeRenew--;

                    $('.sms_code_renew').text(SMSCodeRenew);

                    if (!SMSCodeRenew) {
                        clearInterval(i);
                        btn.prop('disabled', false);
                    }
                }, 1000);

                i2 = setInterval(function () {
                    SMSCodeLifetime--;

                    $('.sms_code_lifetime').text(SMSCodeLifetime);

                    if (!SMSCodeLifetime) {
                        clearInterval(i2);
                    }
                }, 1000);
            } else {
                message(0, data.data);
            }
        });
    }
</script>