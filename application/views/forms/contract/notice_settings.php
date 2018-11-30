<table class="table_form form_settings">
    <tr>
        <td class="gray right" width="170">При блокировке карт:</td>
        <td>
            <label><input type="checkbox" name="notice_email_card" <?=(!empty($settings['EML_CARD_BLOCK']) ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM'] && ($manager['SMS_IS_ON'] || $manager['TELEGRAM_IS_ON'])) {?>
                <br>
                <label>
                    <input type="checkbox" name="notice_sms_card" <?=(!empty($settings['SMS_CARD_BLOCK']) ? 'checked' : '')?>>
                    <?
                    $types = [];
                    if ($manager['SENDER_SMS'] && $manager['SMS_IS_ON']) {$types[] = 'SMS';}
                    if ($manager['TELEGRAM_IS_ON']) {$types[] = 'Telegram';}
                    ?>
                    <?=implode(' / ', $types)?>
                </label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">При пополнении счета:</td>
        <td>
            <label><input type="checkbox" name="notice_email_payment" <?=(!empty($settings['EML_ADD_PAYMENT']) ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM'] && ($manager['SMS_IS_ON'] || $manager['TELEGRAM_IS_ON'])) {?>
                <br>
                <label>
                    <input type="checkbox" name="notice_sms_payment" <?=(!empty($settings['SMS_ADD_PAYMENT']) ? 'checked' : '')?>>
                    <?
                    $types = [];
                    if ($manager['SENDER_SMS'] && $manager['SMS_IS_ON']) {$types[] = 'SMS';}
                    if ($manager['TELEGRAM_IS_ON']) {$types[] = 'Telegram';}
                    ?>
                    <?=implode(' / ', $types)?>
                </label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">При блокировке фирмы:</td>
        <td>
            <label><input type="checkbox" name="notice_email_firm" <?=(!empty($settings['EML_CONTRACT_BLOCK']) ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM'] && ($manager['SMS_IS_ON'] || $manager['TELEGRAM_IS_ON'])) {?>
                <br>
                <label>
                    <input type="checkbox" name="notice_sms_firm" <?=(!empty($settings['SMS_CONTRACT_BLOCK']) ? 'checked' : '')?>>
                    <?
                    $types = [];
                    if ($manager['SENDER_SMS'] && $manager['SMS_IS_ON']) {$types[] = 'SMS';}
                    if ($manager['TELEGRAM_IS_ON']) {$types[] = 'Telegram';}
                    ?>
                    <?=implode(' / ', $types)?>
                </label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">При приближению к критическому порогу:</td>
        <td>
            <label><input type="checkbox" name="notice_email_barrier" <?=(!empty($settings['EML_BLNC_CTRL']) ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM'] && ($manager['SMS_IS_ON'] || $manager['TELEGRAM_IS_ON'])) {?>
                <br>
                <label>
                    <input type="checkbox" name="notice_sms_barrier" <?=(!empty($settings['SMS_BLNC_CTRL']) ? 'checked' : '')?>>
                    <?
                    $types = [];
                    if ($manager['SENDER_SMS'] && $manager['SMS_IS_ON']) {$types[] = 'SMS';}
                    if ($manager['TELEGRAM_IS_ON']) {$types[] = 'Telegram';}
                    ?>
                    <?=implode(' / ', $types)?>
                </label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">Порог:</td>
        <td>
            <input type="text" name="notice_email_barrier_value" value="<?=(!empty($settings['EML_BLNC_CTRL_VALUE']) ? $settings['EML_BLNC_CTRL_VALUE'] : '')?>">
        </td>
    </tr>
    <tr>
        <td class="gray right">Уведомление о балансе:</td>
        <td>
            <label><input type="checkbox" name="notice_email_balance" <?=(!empty($settings['EML_BALANCE']) ? 'checked' : '')?>> E-mail</label>
            <?if ($manager['PHONE_FOR_INFORM'] && ($manager['SMS_IS_ON'] || $manager['TELEGRAM_IS_ON'])) {?>
                <br>
                <label>
                    <input type="checkbox" name="notice_sms_balance" <?=(!empty($settings['SMS_BALANCE']) ? 'checked' : '')?>>
                    <?
                    $types = [];
                    if ($manager['SENDER_SMS'] && $manager['SMS_IS_ON']) {$types[] = 'SMS';}
                    if ($manager['TELEGRAM_IS_ON']) {$types[] = 'Telegram';}
                    ?>
                    <?=implode(' / ', $types)?>
                </label>
            <?}?>
        </td>
    </tr>
    <tr>
        <td class="gray right">В какие дни уведомлять о балансе:</td>
        <td>
            <?
            $days = str_split(!empty($settings['DAYS_NOTE']) ? $settings['DAYS_NOTE'] : '0000000');
            ?>
            <label><input type="checkbox" name="notice_balance_days_monday"    <?=($days[0] ? 'checked' : '')?>> Понедельник</label><br>
            <label><input type="checkbox" name="notice_balance_days_tuesday"   <?=($days[1] ? 'checked' : '')?>> Вторник</label><br>
            <label><input type="checkbox" name="notice_balance_days_wednesday" <?=($days[2] ? 'checked' : '')?>> Среда</label><br>
            <label><input type="checkbox" name="notice_balance_days_thursday"  <?=($days[3] ? 'checked' : '')?>> Черверг</label><br>
            <label><input type="checkbox" name="notice_balance_days_friday"    <?=($days[4] ? 'checked' : '')?>> Пятница</label><br>
            <label><input type="checkbox" name="notice_balance_days_saturday"  <?=($days[5] ? 'checked' : '')?>> Суббота</label><br>
            <label><input type="checkbox" name="notice_balance_days_sunday"    <?=($days[6] ? 'checked' : '')?>> Воскресенье</label>
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
    $(function () {
        $('[type=checkbox]').each(function () {
            renderCheckbox($(this));
        });
    });

    function checkFormContractNoticeSettings(btn)
    {
        var form = btn.closest('.form_settings');
        var params = {
            notice_sms_card:            $('[name=notice_sms_card]', form).is(":checked") ? 1 : 0,
            notice_sms_firm:            $('[name=notice_sms_firm]', form).is(":checked") ? 1 : 0,
            notice_sms_payment:         $('[name=notice_sms_payment]', form).is(":checked") ? 1 : 0,
            notice_sms_barrier:         $('[name=notice_sms_barrier]', form).is(":checked") ? 1 : 0,
            notice_sms_balance:         $('[name=notice_sms_balance]', form).is(":checked") ? 1 : 0,
            notice_email_card:          $('[name=notice_email_card]', form).is(":checked") ? 1 : 0,
            notice_email_firm:          $('[name=notice_email_firm]', form).is(":checked") ? 1 : 0,
            notice_email_payment:       $('[name=notice_email_payment]', form).is(":checked") ? 1 : 0,
            notice_email_barrier:       $('[name=notice_email_barrier]', form).is(":checked") ? 1 : 0,
            notice_email_balance:       $('[name=notice_email_balance]', form).is(":checked") ? 1 : 0,
            notice_email_barrier_value: $('[name=notice_email_barrier_value]', form).val(),
            notice_balance_days: "" +
                ($('[name=notice_balance_days_monday]', form).is(":checked") ? 1 : 0) +
                ($('[name=notice_balance_days_tuesday]', form).is(":checked") ? 1 : 0) +
                ($('[name=notice_balance_days_wednesday]', form).is(":checked") ? 1 : 0) +
                ($('[name=notice_balance_days_thursday]', form).is(":checked") ? 1 : 0) +
                ($('[name=notice_balance_days_friday]', form).is(":checked") ? 1 : 0) +
                ($('[name=notice_balance_days_saturday]', form).is(":checked") ? 1 : 0) +
                ($('[name=notice_balance_days_sunday]', form).is(":checked") ? 1 : 0)
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