<h1>Настройки</h1>

<div class="tabs_block tabs_switcher">
    <div class="tabs">
        <span tab="info" class="tab active"><i class="icon-clients"></i> Информация</span><?if(Access::allow('show_setting_notices')){?><span class="tab" tab="notice"><i class="icon-mail"></i> Оповещения</span><?}?>
    </div>
    <div tab_content="info" class="tabs_content tabs_content_no_padding active">
        <?=$settingsForm?>
    </div>
    <?if(Access::allow('show_setting_notices')){?>
        <div tab_content="notice" class="tabs_content">
            <?=$settingsNoticesForm?>
        </div>
    <?}?>
</div>