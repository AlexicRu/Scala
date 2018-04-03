<h1>Перенос карт</h1>

<div class="block">
    <table>
        <tr>
            <td><b class="f18">Карты старого договора:</b></td>
            <td></td>
            <td><b class="f18">Новый договор:</b></td>
        </tr>
        <tr>
            <td>
                <?=Form::buildField('card_choose_multi', 'card_choose_multi', false, [
                        'show_all' => true
                ])?>
            </td>
            <td class="f24">&nbsp; &xrArr; &nbsp;</td>
            <td>
                <?=Form::buildField('contract_choose_single', 'contract_old')?>
            </td>
        </tr>
        <tr>
            <td>
                <b class="f18">Дата начала переноса:</b><br>
                <?=Form::buildField('datepick', 'date_from', date('d.m.Y'))?>
            </td>
            <td></td>
            <td>
                <b class="f18">Дата окончания переноса:</b><br>
                <?=Form::buildField('datepick', 'date_to')?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <label><input type="checkbox" checked name="transfer_cards"> Перенос карт</label><br>
                <label><input type="checkbox" checked name="transfer_trn"> Перенос транзакций</label><br>
                <label><input type="checkbox" checked name="save_holder"> С сохранением держателей</label>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="btn" onclick="transferCards()">Перенести</span>
            </td>
        </tr>
    </table>
</div>

<script>
    var isAjax = false;

    function transferCards()
    {
        if (isAjax) {
            return false;
        }
        isAjax = true;

        var params = {
            old_contract:           getComboboxValue($('[name=contract_choose_single]'), true),
            new_contract:           getComboboxValue($('[name=contract_old]')),
            cards:                  getComboboxMultiValue($('[name=card_choose_multi]')),
            params: {
                date_from:              $('[name=date_from]').val(),
                date_to:                $('[name=date_to]').val(),
                transfer_cards:         $('[name=transfer_cards]').is(':checked') ? 1 : 0,
                transfer_transactions:  $('[name=transfer_trn]').is(':checked') ? 1 : 0,
                save_holder:            $('[name=save_holder]').is(':checked') ? 1 : 0,
            }
        };

        $.post('/administration/cards-transfer', params, function (data) {
            if (data.success) {
                message(1, 'Перенос прошел успешно');
            } else {
                message(0, 'Ошибка переноса');
            }
            isAjax = false;
        });
    }
</script>