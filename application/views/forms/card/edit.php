<table class="table_form form_card_edit">
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse btn_card_edit_go">Сохранить</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.btn_card_edit_go').on('click', function(){
            var params = {
                contract_id:    $('[name=contracts_list]').val(),
                card_id:        <?=$card['CARD_ID']?>,
            };

            /*if(params.card_id == ''){
                message(0, 'Введите номер карты');
                return false;
            }*/

            $.post('/clients/card_edit', {params:params}, function(data){
                if(data.success){
                    message(1, 'Карта успешно обновлена');
                    loadContract('cards');
                }else{
                    message(0, 'Ошибка обновления карты');
                }
            });
        });
    });
</script>