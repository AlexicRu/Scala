<?
$postfix = $card['CARD_ID'];
?>
<table class="table_form form_card_edit">
    <tr>
        <td class="gray right" width="170">Держатель:</td>
        <td>
            <input type="text" name="card_edit_holder" class="input_big input_grand" value="<?=Text::quotesForForms($card['HOLDER'])?>" maxlength="200">
        </td>
    </tr>
    <tr>
        <td class="gray right">Выдана держателю:</td>
        <td>
            <input type="text" class="input_big datepicker" readonly name="card_edit_date" value="<?=$card['DATE_HOLDER']?>">
        </td>
    </tr>
    <tr>
        <td class="gray right">Комментарий:</td>
        <td>
            <textarea name="card_edit_holder_comment" style="width: 100%"><?=$card['CARD_COMMENT']?></textarea>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="cardEditHolderGo_<?=$postfix?>($(this))"><i class="icon-ok"></i> Сохранить</span>

            <?if(empty($card['CHANGE_LIMIT_AVAILABLE']) || !Access::allow('clients_card-edit-limits')){?>
                <span class="btn btn_red fancy_close">Отмена</span>
            <?}?>
        </td>
    </tr>
</table>

<script>
    $(function () {
        $('[name=card_edit_date]').each(function () {
            renderDatePicker($(this));
        });
    });

    function cardEditHolderGo_<?=$postfix?>(t)
    {
        var form = t.closest('.form_card_edit');
        var params = {
            contract_id : $('[name=contracts_list]').val(),
            card_id     : $('.tab_v.active').attr('tab'),
            holder      : $('[name=card_edit_holder]', form).val(),
            date        : $('[name=card_edit_date]', form).val(),
            comment     : $('[name=card_edit_holder_comment]', form).val(),
        };

        if(params.date == false){
            message(0, 'Заполните дату');
            return;
        }

        $.post('/clients/card-edit-holder', params, function (data) {
            if (data.success) {
                message(1, 'Держатель карты успешно обновлен');
                $.fancybox.close();
                cardLoad($('.tab_v.active'), true);
                $('.tab_v.active div.gray').text(params.holder);
            } else {
                message(0, 'Ошибка обновления держателя карты');

                if(data.data){
                    for(var i in data.data){
                        message(0, data.data[i].text);
                    }
                }
            }
        });
    }
</script>