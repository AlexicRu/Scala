function searchManagerClients(input, managerId)
{
    showManagersClients(managerId, {search: input.val()}, true)
}

function managerStateToggle(managerId, t)
{
    var comment = '';

    if(t.hasClass('btn_red')){
        comment = prompt('Причина блокировки:');
    }

    if(comment != null) {
        var params = {
            manager_id: managerId,
            comment: comment
        };

        $.post('/managers/manager-toggle', {params:params}, function (data) {
            if (data.success) {
                t.toggleClass('btn_red').toggleClass('btn_green').find('span').toggle();

                message(1, 'Статус менеджера изменен');
            } else {
                message(0, 'Ошибка обновления');
            }
        });
    }
}

function showManagersClients(managerId, params, force)
{
    var block = $('[tab_content=clients][manager_id='+ managerId +'] .client_list');

    if(block.html() != '' && !force){
        return true;
    }

    block.empty().addClass(CLASS_LOADING);

    $.post('/managers/load-clients', { manager_id: managerId, params: params }, function (data) {
        block.removeClass(CLASS_LOADING);
        block.html(data);

        renderScroll($('.tabs_managers .scroll'));
    });
}

function showManagersReports(managerId, params, force)
{
    var block = $('[tab_content=reports][manager_id='+ managerId +'] .report_list');

    if(block.html() != '' && !force){
        return true;
    }

    block.empty().addClass(CLASS_LOADING);

    $.post('/managers/load-reports', { manager_id: managerId, params: params }, function (data) {
        block.removeClass(CLASS_LOADING);
        block.html(data);

        renderScroll($('.tabs_managers .scroll'));
        loadReportList($('#manager_add_reports').find('input[type=text]'));
    });
}

function delManagersClient(btn)
{
    if(!confirm('Удаляем клиента?')){
        return false;
    }
    var line = btn.closest('[client_id]');
    var clientId = line.attr('client_id');
    var managerId = line.attr('manager_id');

    var params = {
        client_id: clientId,
        manager_id: managerId
    };

    $.post('/managers/del-client', params, function (data) {
        if(data.success){
            message(1, 'Клиент успешно удален');
            line.fadeOut();
        }else{
            message(0, errorStr('Ошибка удаления клиента', data.data));
        }
    });
}

function delManagersReport(btn)
{
    if(!confirm('Удаляем отчет?')){
        return false;
    }
    var line = btn.closest('[report_id]');
    var reportId = line.attr('report_id');
    var managerId = line.attr('manager_id');

    var params = {
        report_id: reportId,
        manager_id: managerId
    };

    $.post('/managers/del-report', params, function (data) {
        if(data.success){
            message(1, 'Отчет успешно удален');
            line.fadeOut();
        }else{
            message(0, errorStr('Ошибка удаления отчета', data.data));
        }
    });
}

function saveManagerClientContractBinds(btn)
{
    var line = btn.closest('[client_id]');
    var clientId = line.attr('client_id');
    var managerId = line.attr('manager_id');

    var binds = getComboboxMultiValue($('[name=manager_clients_contract_binds'+ clientId +']', line));

    var params = {
        client_id: clientId,
        manager_id: managerId,
        binds: binds
    };

    $.post('/managers/edit-manager-clients-contract-binds', params, function(data) {
        if (data.success) {
            message(1, 'Доступы менеджера к договорам обновлены');
        } else {
            message(0, 'Ошибка обновления доступов');
        }
    });
}

var ajax = false;
function loadReportList(input)
{
    if(ajax){
        ajax.abort();
    }

    var block = input.closest('.reports_list_autocomplete_block');
    var list = block.find('.found_items_list');

    list.html('').addClass('block_loading');

    var params = {
        search: input.val(),
        add_reports:1,
        manager_id: $('.tab_content[manager_id]:visible').attr('manager_id')
    };

    ajax = $.post('/managers/managers-reports', {params:params}, function (data) {
        list.removeClass('block_loading');

        for(var i in data.data){
            var tpl = $('<div class="item_found_row"><span><input type="checkbox" class="found_report" onchange="checkFoundItem($(this))"></span><span class="ifr_label"><span class="label" /></span><span class="ifr_name" /></div>');

            tpl.find('.ifr_label span').text(data.data[i].global_type_name).addClass(data.data[i].global_type_label);
            tpl.find('.ifr_name').text(data.data[i].WEB_NAME);
            tpl.attr('item_id', data.data[i].REPORT_ID);
            tpl.data('item_name', data.data[i].WEB_NAME);

            var ids = collectFoundIds(block);

            if(ids.indexOf(data.data[i].REPORT_ID) != -1){
                tpl.find('[type=checkbox]').prop('checked', true);
            }

            renderCheckbox(tpl.find('[type=checkbox]'));

            tpl.appendTo(list);
        }

        ajax = false;
    });
}

function managerAddReports(btn)
{
    var block = btn.closest('.items_list_autocomplete_block');

    var params = {
        ids:        collectFoundIds(block),
        manager_id: $('.tab_content[manager_id]:visible').attr('manager_id')
    };

    if(params.ids.length == 0){
        message(0, 'Выберите отчеты');
        endSubmitForm();
        return false;
    }

    $.post('/managers/add-reports', {params:params}, function (data) {
        if(data.success){
            message(1, 'Отчеты успешно добавлены');
            $.fancybox.close();
            showManagersReports(params.manager_id, [], true);

            $('.selected_items_list', block).html('');
            $('.found_items_list', block).html('');
            block.find('input').val('');
        }else{
            var txt = '';

            if(data.data == 2){
                txt = '. Отчет уже закреплен за менеджером';
            }

            message(0, 'Ошибка добавления отчетов' + txt);
        }
        endSubmitForm();
    });
}

function loadClientList(input)
{
    if(ajax){
        ajax.abort();
    }

    var block = input.closest('.items_list_autocomplete_block');
    var list = block.find('.found_items_list');

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

    ajax = $.post('/managers/managers-clients', {params:params}, function (data) {
        list.removeClass('block_loading');

        for(var i in data.data){
            var tpl = $('<div class="item_found_row"><span><input type="checkbox" class="found_client" onchange="checkFoundItem($(this))"></span><span class="ifr_id gray" /><span class="ifr_name" /></div>');
            tpl.find('.ifr_id').text(data.data[i].CLIENT_ID);
            tpl.find('.ifr_name').text(data.data[i].CLIENT_NAME);
            tpl.attr('item_id', data.data[i].CLIENT_ID);
            tpl.data('item_name', data.data[i].CLIENT_NAME);

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

function managerAddClients(btn)
{
    var block = btn.closest('.clients_list_autocomplete_block');

    var params = {
        ids:        collectFoundIds(block),
        manager_id: $('.tab_content[manager_id]:visible').attr('manager_id')
    };

    if(params.ids.length == 0){
        message(0, 'Выберите клиентов');
        return false;
    }

    $.post('/managers/add-clients', {params:params}, function (data) {
        if(data.success){
            message(1, 'Клиенты успешно добавлены');
            $.fancybox.close();
            showManagersClients(params.manager_id, [], true);

            $('.selected_items_list', block).html('');
            $('.found_items_list', block).html('');
            block.find('input').val('');
        }else{
            var txt = '';

            if(data.data == 3){
                txt = '. Клиент уже закреплен за другим менеджером по продажам';
            }

            message(0, 'Ошибка добавления клиентов' + txt);
        }
    });
}