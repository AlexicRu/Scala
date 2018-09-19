<table class="table_form form_settings">
    <tr>
        <td class="gray right" width="170">При блокировке карт:</td>
        <td>
            <label><input type="checkbox" name="notice_email_card" <?=($settings['EML_CARD_BLOCK'] ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM']) {?>
            <br><label><input type="checkbox" name="notice_sms_card" <?=($settings['SMS_CARD_BLOCK'] ? 'checked' : '')?>> SMS / Telegram</label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">При блокировке фирмы:</td>
        <td>
            <label><input type="checkbox" name="notice_email_firm" <?=($settings['EML_CONTRACT_BLOCK'] ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM']) {?>
            <br><label><input type="checkbox" name="notice_sms_firm" <?=($settings['SMS_CONTRACT_BLOCK'] ? 'checked' : '')?>> SMS / Telegram</label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">При блокировке фирмы:</td>
        <td>
            <label><input type="checkbox" name="notice_email_firm" <?=($settings['EML_CONTRACT_BLOCK'] ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM']) {?>
                <br><label><input type="checkbox" name="notice_sms_firm" <?=($settings['SMS_CONTRACT_BLOCK'] ? 'checked' : '')?>> SMS / Telegram</label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">При приближению к критическому порогу:</td>
        <td>
            <label><input type="checkbox" name="notice_email_barrier" <?=($settings['EML_BLNC_CTRL'] ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM']) {?>
            <br><label><input type="checkbox" name="notice_sms_barrier" <?=($settings['SMS_BLNC_CTRL'] ? 'checked' : '')?>> SMS / Telegram</label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">Порог:</td>
        <td>
            <input type="text" name="notice_email_barrier_value" value="<?=$settings['EML_BLNC_CTRL_VALUE']?>">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="submitForm($(this), checkFormContractNoticeSettings)"><i class="icon-ok"></i> Сохранить</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    function checkFormContractNoticeSettings(btn)
    {
        var form = btn.closest('.form_settings');
        var params = {
            notice_sms_card:            $('[name=notice_sms_card]', form).is(":checked") ? 1 : 0,
            notice_sms_firm:            $('[name=notice_sms_firm]', form).is(":checked") ? 1 : 0,
            notice_sms_barrier:         $('[name=notice_sms_barrier]', form).is(":checked") ? 1 : 0,
            notice_email_card:          $('[name=notice_email_card]', form).is(":checked") ? 1 : 0,
            notice_email_firm:          $('[name=notice_email_firm]', form).is(":checked") ? 1 : 0,
            notice_email_barrier:       $('[name=notice_email_barrier]', form).is(":checked") ? 1 : 0,
            notice_email_barrier_value: $('[name=notice_email_barrier_value]', form).val()
        };

        $.post('/clients/edit-contract-notices', {contract_id: $('[name=contracts_list]').val(), params:params}, function (data) {
            if(data.success){
                message(1, 'Настройки уведомлений обновлены');
                $.fancybox.close();
            }else{
                message(0, 'Ошибка настройки уведомлений');
            }
        });

        return false;
    }
</script>