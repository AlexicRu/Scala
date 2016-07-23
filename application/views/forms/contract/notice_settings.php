<form method="post" onsubmit="return checkFormContractNoticeSettings($(this));">
    <input type="hidden" name="form_type" value="settings_notices">

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
    <div class="switch_block">
        <div class="sb_title">
            <span class="sb_block"><input type="checkbox" class="switch" checked></span>
            <b>Оповещения по e-mail</b>
        </div>
        <div class="sb_content"><span class="sb_block"><input type="checkbox"></span> При блокировке карт</div>
        <div class="sb_content"><span class="sb_block"><input type="checkbox" checked></span> При блокировке фирмы</div>
        <div class="sb_content">
            <span class="sb_block"><input type="checkbox"></span>
            При приближению к критическому порогу<br>
            <small class="gray">Порог:</small> <input type="text" placeholder="1500">
        </div>
    </div>
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
    <div class="switch_block">
        <span class="sb_block"></span>
        <button class="btn btn_green btn_reverse btn_manager_settings_go"><i class="icon-ok"></i> Сохранить</button>
    </div>
</form>

<script>
    function checkFormContractNoticeSettings(form)
    {
        return true;
    }
</script>