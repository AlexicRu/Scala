<table class="table_form form_add_supplier">
    <tr>
        <td class="gray right" width="170">Наименование:</td>
        <td>
            <input type="text" name="add_supplier_name" class="input_big">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="addSupplier($(this))">+ Добавить поставщика</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>

<script>
    function addSupplier(btn)
    {
        var block = btn.closest('.form_add_supplier');

        var params = {
            name: $('[name=add_supplier_name]', block).val()
        };

        if(params.name == ''){
            message(0, 'Введите наименование поставщика');
            return false;
        }

        $.post('/suppliers/supplier_add', {params:params}, function(data){
            if(data.success){
                message(1, 'Поставщик успешно добавлен');
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }else{
                message(0, 'Ошибка добавления поставщика');
            }
        });
    }
</script>