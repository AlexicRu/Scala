<div class="clients_list_autocomplete_block">
    <div class="input_out">
        <div class="input_with_icon">
            <i class="icon-find"></i>
            <input type="text" class="input_big" placeholder="Начните вводить имя клиента" onkeyup="loadClientList($(this))">
        </div>
    </div>
    <div class="found_clients_list"></div>
    <div class="selected_clients_list"></div>

    <div class="right">
        <button class="btn btn_reverse btn_manager_add_clients_go" onclick="managerAddClients($(this))"><i class="icon-ok"></i> Добавить</button>
        <span class="btn btn_red fancy_close">Отмена</span>
    </div>
</div>

<script>
    var ajax = false;
    function loadClientList(input)
    {
        if(ajax){
            ajax.abort();
        }

        var block = input.closest('.clients_list_autocomplete_block');
        var list = block.find('.found_clients_list');

        if(input.val() == ''){
            list.html('');
            return;
        }

        list.html('').addClass('block_loading');

        var params = {
            search: input.val(),
            add_clients:1,
            manager_id: $('.tab_content[manager_id]:visible').attr('manager_id')
        };

        ajax = $.post('/managers/managers_clients', {params:params}, function (data) {
            list.removeClass('block_loading');

            for(var i in data.data){
                var tpl = $('<div class="client_found_row"><span><input type="checkbox" class="found_client" onchange="checkFoundClient($(this))"></span><span class="cfr_id gray" /><span class="cfr_name" /></div>');

                tpl.find('.cfr_id').text(data.data[i].CLIENT_ID);
                tpl.find('.cfr_name').text(data.data[i].LONG_NAME ? data.data[i].LONG_NAME : data.data[i].CLIENT_NAME);
                tpl.attr('client_id', data.data[i].CLIENT_ID);
                tpl.data('client_name', data.data[i].LONG_NAME ? data.data[i].LONG_NAME : data.data[i].CLIENT_NAME);

                var ids = collectFoundIds(block);

                if(ids.indexOf(data.data[i].CLIENT_ID) != -1){
                    tpl.find('[type=checkbox]').prop('checked', true);
                }

                renderCheckbox(tpl.find('[type=checkbox]'));

                tpl.appendTo(list);
            }

            ajax = false;
        });
    }

    function collectFoundIds(block)
    {
        var list = $('.selected_clients_list', block);

        var ids = [];

        $('.scl_item').each(function () {
            ids.push($(this).attr('client_id'));
        });

        return ids;
    }

    function checkFoundClient(check)
    {
        var block = check.closest('.clients_list_autocomplete_block');
        var row = check.closest('.client_found_row');
        var list = $('.selected_clients_list', block);

        if(check.is(':checked')) {
            //add
            var tpl = $('<div class="scl_item"><span class="scli_close" onclick="uncheckFoundClient($(this))">&times;</span></div>');

            tpl.attr('client_id', row.attr('client_id')).prepend(row.data('client_name'));

            tpl.appendTo(list);
        } else {
            //remove
            list.find('[client_id='+ row.attr('client_id') +']').remove();
        }
    }

    function uncheckFoundClient(close)
    {
        var block = close.closest('.clients_list_autocomplete_block');
        var list = $('.selected_clients_list', block);

        var item = close.closest('.scl_item');

        $('.client_found_row[client_id='+ item.attr('client_id') +'] [type=checkbox]').click();
    }

    function managerAddClients(btn)
    {
        var block = btn.closest('.clients_list_autocomplete_block');

        var params = {
            ids:        collectFoundIds(block),
            manager_id: $('.tab_content[manager_id]:visible').attr('manager_id')
        };

        if(params.ids.length == 0){
            message(0, 'Выдерите клиентов');
            return false;
        }

        $.post('/managers/add_clients', {params:params}, function (data) {
            if(data.success){
                message(1, 'Клиенты успешно добавлены');
                $.fancybox.close();
                $('[tab_content=clients][manager_id='+ params.manager_id +'] .client_list').html('');
                showManagersClients(params.manager_id);

                $('.selected_clients_list', block).html('');
                $('.found_clients_list', block).html('');
                block.find('input').val('');
            }else{
                message(0, 'Ошибка добавления клиентов');
            }
        });
    }
</script>