<table class="table_form form_add_supplier_contract">
    <tr>
        <td class="gray right" width="170">Номер:</td>
        <td>
            <input type="text" name="add_supplier_contract_name" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Дата начала:</td>
        <td>
            <input type="text" class="input_big datepicker" readonly name="add_supplier_contract_date_start" value="<?=date('d.m.Y')?>">
        </td>
    </tr>
    <tr>
        <td class="gray right" width="170">Дата окончания:</td>
        <td>
            <input type="text" class="input_big datepicker" readonly name="add_supplier_contract_date_end">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <span class="btn btn_reverse" onclick="addSupplierContract($(this))">+ Добавить договор</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>
<small><i>* - Дату окончания оставить пустой в случае бессрочного договора</i></small>
<script>
    function addSupplierContract(btn)
    {
        var block = btn.closest('.form_add_supplier_contract');

        var params = {
            supplier_id:    supplierId,
            name:           $('[name=add_supplier_contract_name]', block).val(),
            date_start:     $('[name=add_supplier_contract_date_start]', block).val(),
            date_end:       $('[name=add_supplier_contract_date_end]', block).val(),
        };

        if(params.name == ''){
            message(0, 'Введите название договора');
            return false;
        }
        if(params.date_start == ''){
            message(0, 'Введите начала действия');
            return false;
        }

        $.post('/suppliers/contract_add', {params:params}, function(data){
            if(data.success){
                message(1, 'Договор успешно добавлен');
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }else{
                message(0, data.data ? data.data : 'Ошибка добавления договора');
            }
        });
    }
</script>