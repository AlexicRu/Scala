<div class="tabs_block tabs_switcher">
    <div class="tabs">
        <span tab="info" class="tab active">Информация</span><span class="tab" tab="clients">Клиенты</span>
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
        <div tab_content="clients" class="tab_content"></div>
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
</script>