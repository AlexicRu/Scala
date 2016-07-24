<h1>Настройки</h1>

<div class="tabs_block tabs_switcher">
    <div class="tabs">
        <span tab="info" class="tab active"><i class="icon-user"></i> Информация</span><?if(Access::allow('show_setting_notices')){?><span class="tab" tab="notice"><i class="icon-notifications"></i> Оповещения</span><?}?>
    </div>
    <div class="tabs_content tabs_content_no_padding">
        <div tab_content="info" class="tab_content active">
            <?=$managerSettingsForm?>
        </div>
    </div>
</div>