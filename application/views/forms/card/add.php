<table class="table_form form_add_card">
    <tr>
        <td class="gray right" width="170">Номер карты:</td>
        <td>
            <?=Common::buildFormField(false, 'card_available_choose_single', 'add_card_id', false, 'input_big')?>
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Владелец:</td>
        <td>
            <input type="text" name="add_card_holder" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Срок действия:</td>
        <td>
            <input type="text" class="input_big datepicker" readonly name="add_card_expire_date">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse btn_add_card_go">+ Добавить карту</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.btn_add_card_go').on('click', function(){
            var params = {
                contract_id:    $('[name=contracts_list]').val(),
                card_id:        $('[name=add_card_id]').val(),
                holder:         $('[name=add_card_holder]').val(),
                expire_date:    $('[name=add_card_expire_date]').val()
            };

            if(params.card_id == ''){
                message(0, 'Введите номер карты');
                return false;
            }

            $.post('/clients/card_add', {params:params}, function(data){
                if(data.success){
                    message(1, 'Карта успешно добавлена');
                    loadContract('cards');
                }else{
                    message(0, data.data ? data.data : 'Ошибка добавления карты');
                }
            });
        });
    });
</script>