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

        </div>
        <div tab_content="clients" class="tab_content" manager_id="<?=$managerId?>"></div>
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
        var block = $('[tab_content=clients][manager_id='+ managerId +']');

        if(block.html() != ''){
            return true;
        }

        block.addClass('block_loading');

        $.post('/managers/load_clients', { manager_id: managerId }, function (data) {
            block.removeClass('block_loading');

            if(data.success){
                var clients = data.data;

                if(clients.length == 0){
                    $('<div class="line_inner">Клиенты не найдены</div>').appendTo(block);
                }else{
                    for(var i in clients) {
                        var tpl = $('<div class="line_inner"><span class="gray"></span> &nbsp;&nbsp;&nbsp; <b></b><div class="fr"><a href="#" class="red del" onclick="delManagersClient($(this))">Удалить <i class="icon-cancel"></i></a></div></div>');

                        tpl.find('.gray:first').text(clients[i].CLIENT_ID);
                        tpl.find('b:first').text(clients[i].CLIENT_NAME);
                        tpl.attr('client_id', clients[i].CLIENT_ID);
                        tpl.attr('manager_id', managerId);
                        tpl.appendTo(block);
                    }
                }

            }else{
                message(0, 'Ошибка загрузки клиентов');
            }
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
</script>