<form method="post" onsubmit="return checkFormContractNoticeSettings($(this));">
    <input type="hidden" name="form_type" value="settings_notices">

    <?/*?>
    <div class="switch_block">
        <div class="sb_title">
            <span class="sb_block"><input type="checkbox" class="switch" checked></span>
            <b>Периодичность отправки отчетов</b>
        </div>
        <div class="sb_content">
            <span class="sb_block"></span>
            <select>
                <option>Раз в неделю</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <small class="gray">День:</small>
            <select>
                <option>Вторник</option>
            </select>
        </div>
    </div>
<?*/?>

    <div class="switch_block">
        <div class="sb_title">
            <!--span class="sb_block"><input type="checkbox" class="switch" checked name="notice_email_fl"></span-->
            <b>Уведомления по e-mail</b>
        </div>
        <div class="sb_content">
            <label class="sb_block"><input type="checkbox" name="notice_email_card" <?=($settings['EML_CARD_BLOCK'] ? 'checked' : '')?>></label>
            При блокировке карт
        </div>
        <div class="sb_content">
            <label class="sb_block"><input type="checkbox" name="notice_email_firm"  <?=($settings['EML_CONTRACT_BLOCK'] ? 'checked' : '')?>></label>
            При блокировке фирмы
        </div>
        <div class="sb_content">
            <label class="sb_block"><input type="checkbox" name="notice_email_barrier"  <?=($settings['EML_BLNC_CTRL'] ? 'checked' : '')?>></label>
            При приближению к критическому порогу<br>
            <small class="gray">Порог:</small> <input type="text" name="notice_email_barrier_value" value="<?=$settings['EML_BLNC_CTRL_VALUE']?>">
        </div>
    </div>
    <?/*?>
    <div class="switch_block">
        <div class="sb_title">
            <span class="sb_block"><input type="checkbox" class="switch"></span>
            <b>Оповещения по SMS</b> <a href="#">Цены на SMS оповещения</a>
        </div>
        <div class="sb_content sb_disabled"><span class="sb_block"><input type="checkbox" disabled></span> При блокировке карт</div>
        <div class="sb_content sb_disabled"><span class="sb_block"><input type="checkbox" disabled checked></span> При блокировке фирмы</div>
        <div class="sb_content sb_disabled">
            <span class="sb_block"><input type="checkbox" disabled></span>
            При приближению к критическому порогу<br>
            <small class="gray">Порог:</small> <input type="text" placeholder="1500" disabled>
        </div>
        <div class="sb_content sb_disabled"><span class="sb_block"><input type="checkbox" disabled></span> Пополнение счета</div>
        <div class="sb_content sb_disabled"><span class="sb_block"><input type="checkbox" disabled></span> Транзакции по карте</div>
    </div>
<?*/?>
    <div class="switch_block">
        <span class="sb_block"></span>
        <button class="btn btn_green btn_reverse btn_manager_settings_go"><i class="icon-ok"></i> Сохранить</button>
    </div>
</form>

<script>
    function checkFormContractNoticeSettings(form)
    {
        var params = {
            notice_email_card:          $('[name=notice_email_card]', form).is(":checked") ? 1 : 0,
            notice_email_firm:          $('[name=notice_email_firm]', form).is(":checked") ? 1 : 0,
            notice_email_barrier:       $('[name=notice_email_barrier]', form).is(":checked") ? 1 : 0,
            notice_email_barrier_value: $('[name=notice_email_barrier_value]', form).val()
        };

        $.post('/clients/edit_contract_notices', {contract_id: $('[name=contracts_list]').val(), params:params}, function (data) {
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