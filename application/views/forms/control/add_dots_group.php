<table class="table_form form_add_dots_group">
    <tr>
        <td class="gray right" width="170">Название группы:</td>
        <td>
            <input type="text" name="add_dots_group_name" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Тип:</td>
        <td>
            <select class="select_big" name="add_dots_group_type">
                <?foreach(Model_Dot::getGroupTypesNames() as $groupsType => $groupsTypesName){?>
                    <option value="<?=$groupsType?>"><?=$groupsTypesName?></option>
                <?}?>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse btn_add_dots_group_go">+ Добавить группу</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    $(function(){
        $('.btn_add_dots_group_go').on('click', function(){
            var params = {
                name:        $('[name=add_dots_group_name]').val(),
                group_type:  $('[name=add_dots_group_type]').val()
            };

            if(params.name == ''){
                message(0, 'Введите название группы');
                return false;
            }

            $.post('/control/add-dots-group', {params:params}, function(data){
                if(data.success){
                    message(1, 'Группа успешно добавлена');
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }else{
                    message(0, data.data ? data.data : 'Ошибка добавления группы');
                }
            });
        });
    });
</script>