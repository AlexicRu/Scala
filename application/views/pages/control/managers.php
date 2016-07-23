<h1>Менеджеры</h1>

<div class="tabs_vertical_block tabs_switcher tabs_managers">
    <div class="tabs_v">
        <div class="tab_v"><div>
            <div class="input_with_icon"><i class="icon-find"></i><form><input type="text" name="m_search" class="input_big input_messages" placeholder="Поиск..." value="<?=$mSearch?>"></form></div>
        </div></div>

        <div class="tab_v"><div>
            <a href="#manager_user" class="fancy">Добавить менеджера</a>
        </div></div>

        <div class="scroll">
            <?if(empty($managers)){?>
                <div class="tab_v"><div>
                    <span class="gray">Менеджеры не найдены</span>
                </div></div>
            <?}else{?>
                <?foreach($managers as $key => $manager){?>
                    <div class="tab_v" tab="manager<?=$manager['MANAGER_ID']?>"><div>
                        <span class="gray">[<?=$manager['MANAGER_ID']?>]</span>
                        <?=$manager['MANAGER_SURNAME']?>
                        <?=$manager['MANAGER_NAME']?>
                        <?=$manager['MANAGER_MIDDLENAME']?>
                    </div></div>
                <?}?>
            <?}?>
        </div>
    </div>
    <div class="tabs_v_content tabs_content_no_padding">
        <?if(!empty($managers)){?>
            <?foreach($managers as $key => $manager){?>
                <div class="tab_v_content" tab_content="manager<?=$manager['MANAGER_ID']?>"></div>
            <?}?>
        <?}?>
    </div>
</div>

<script>
    $(function(){
        renderScroll($('.tabs_managers .scroll'), -125);

        $(".tabs_managers .tabs_v .scroll > [tab]").on('click', function(){
            var t = $(this);

            loadManager(t);
        });
        
        var clicked = false;
        $(".tabs_managers .tabs_v .scroll > [tab]").each(function(){
            if(clicked){
                return;
            }
            var t = $(this);

            if(!clicked){
                clicked = true;
                t.click();
            }
        });
    });
    
    function loadManager(tab, force)
    {
        var tabsBlock = $(".tabs_managers");
        var managerId = tab.attr('tab').replace('manager', '');
        var tabContent = $("[tab_content=manager"+ managerId +"]", tabsBlock);

        if(tabContent.text() == '' || force == true){
            tabContent.empty().parent().addClass('block_loading');

            $.post('/control/manager/' + managerId, {}, function(data){
                tabContent.html(data).parent().removeClass('block_loading');
            });
        }
    }
</script>