<script src="<?=Common::getAssetsLink()?>js/managers/managers.js"></script>

<div class="tabs_block tabs_switcher">
    <div class="tabs">
        <span tab="info" class="tab active">Информация</span><span class="tab" tab="clients" onclick="showManagersClients(<?=$managerId?>)">Клиенты</span><?if (Access::allow('managers_load-reports')) {?><span class="tab" tab="reports" onclick="showManagersReports(<?=$managerId?>)">Отчеты</span><?}?>
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
        <div tab_content="clients" class="tab_content" manager_id="<?=$managerId?>">
            <div class="fr clients_btn">
                <a href="#manager_add_clients" class="fancy btn">Добавить клиентов</a>
            </div>
            <div class="clients__search">
                <div class="input_with_icon">
                    <i class="icon-find"></i>
                    <input type="text" onkeypress="if(event.keyCode == 13){searchManagerClients($(this), <?=$managerId?>)}" class="input_big input_messages" placeholder="Поиск...">
                </div>
            </div>
            <div class="clr"></div>
            <div class="client_list"></div>
        </div>
        <?if (Access::allow('managers_load-reports')) {?>
        <div tab_content="reports" class="tab_content" manager_id="<?=$managerId?>">
            <div class="fr clients_btn">
                <a href="#manager_add_reports" class="fancy btn">Добавить отчеты</a>
            </div>
            <div class="clr"></div>
            <div class="report_list"></div>
        </div>
        <?}?>
    </div>
</div>

<?=$popupManagerAddClients?>
<?=$popupManagerAddReports?>