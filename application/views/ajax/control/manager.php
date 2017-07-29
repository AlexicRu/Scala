<div class="tabs_block tabs_switcher">
    <div class="tabs">
        <span tab="info" class="tab active">Информация</span><span class="tab" tab="clients" onclick="showManagersClients(<?=$managerId?>)">Клиенты</span>
    </div>
    <div class="tabs_content">
        <div tab_content="info" class="tab_content active">

            <?if(Access::allow('manager_toggle')) {?>
                <div class="fr">
                    <button class="btn <?=($manager['STATE_ID'] != Model_Manager::STATE_MANAGER_ACTIVE ? 'btn_green' : 'btn_red')?>" onclick="managerStateToggle(<?=$manager['MANAGER_ID']?>, $(this))">
                        <span <?=($manager['STATE_ID'] != Model_Manager::STATE_MANAGER_ACTIVE ? 'style="display:none"' : '')?>><i class="icon-block"></i> Заблокировать</span>
                        <span <?=($manager['STATE_ID'] == Model_Manager::STATE_MANAGER_ACTIVE ? 'style="display:none"' : '')?>><i class="icon-backblock"></i> Разблокировать</span>
                    </button>
                    <br><br>
                </div>
            <?}?>

            <h2>ID: <?=$managerId?></h2>

            <?=$managerSettingsForm?>
            <?=$popupManagerAddClients?>

        </div>
        <div tab_content="clients" class="tab_content" manager_id="<?=$managerId?>">
            <div class="clients_btn">
                <a href="#manager_add_clients" class="fancy btn">Добавить клиентов</a>
            </div>
            <div class="client_list"></div>
        </div>
    </div>
</div>

<script>
    <?if(Access::allow('manager_toggle')) {?>
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

            $.post('/managers/manager_toggle', {params:params}, function (data) {
                if (data.success) {
                    t.toggleClass('btn_red').toggleClass('btn_green').find('span').toggle();

                    message(1, 'Статус менеджера изменен');
                } else {
                    message(0, 'Ошибка обновления');
                }
            });
        }
    }
    <?}?>

    function showManagersClients(managerId)
    {
        var block = $('[tab_content=clients][manager_id='+ managerId +'] .client_list');

        if(block.html() != ''){
            return true;
        }

        block.addClass('block_loading');

        $.post('/managers/load_clients', { manager_id: managerId }, function (data) {
            block.removeClass('block_loading');
            block.html(data);

            renderScroll($('.tabs_managers .scroll'));
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

        $.post('/managers/del_client', params, function (data) {
            if(data.success){
                message(1, 'Клиент успешно удален');
                line.fadeOut();
            }else{
                message(0, errorStr('Ошибка удаления клиента', data.data));
            }
        });
    }

    <?if(Access::allow('managers_edit_manager_clients_contract_binds')) {?>
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

        $.post('/managers/edit_manager_clients_contract_binds', params, function(data) {
            if (data.success) {
                message(1, 'Доступы менеджера к договорам обновлены');
            } else {
                message(0, 'Ошибка обновления доступов');
            }
        });
    }
    <?}?>
</script>