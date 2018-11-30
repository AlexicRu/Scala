<table class="table_form form_edit_cards_group">
    <input type="hidden" name="edit_cards_group_id">
    <tr>
        <td class="gray right" width="170">Название группы:</td>
        <td>
            <input type="text" name="edit_cards_group_name" class="input_big">
        </td>
    </tr>
    <?if (in_array($user['ROLE_ID'], Access::$adminRoles)) {?>
        <tr>
            <td class="gray right">Тип группы:</td>
            <td>
                <select class="select_big" name="edit_cards_group_type">
                    <?foreach (Model_Card::$cardsGroupsTypes as $id => $name) {?>
                        <option value="<?=$id?>"><?=$name?></option>
                    <?}?>
                </select>
            </td>
        </tr>
    <?} else {?>
        <input type="hidden" name="edit_cards_group_type" value="<?=Model_Card::CARD_GROUP_TYPE_USER?>">
    <?}?>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse btn_edit_cards_group_go"><i class="icon-ok"></i> Сохранить</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.btn_edit_cards_group_go').on('click', function(){
            var params = {
                group_id:    $('[name=edit_cards_group_id]').val(),
                name:        $('[name=edit_cards_group_name]').val(),
                type:        $('[name=edit_cards_group_type]').val(),
            };

            if(params.name == ''){
                message(0, 'Введите название группы');
                return false;
            }

            $.post('/control/edit-cards-group', {params:params}, function(data){
                if(data.success){
                    message(1, 'Группа успешно обновлена');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }else{
                    message(0, data.data ? data.data : 'Ошибка обновления группы');
                }
            });
        });
    });
</script>