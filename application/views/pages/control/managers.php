<h1><?=Lng::phrase('managers')?></h1>

<div class="tabs_vertical_block tabs_switcher tabs_managers">
    <div class="tabs_v">
        <form>
            <div class="tab_v tab_v_small"><div>
                <div class="input_with_icon"><i class="icon-find"></i><input type="text" name="filter[search]" class="input_big input_messages" placeholder="Поиск..." value="<?=(empty($filter['search']) ? '' : $filter['search'])?>"></div>
            </div></div>
            <div class="tab_v tab_v_filter filter_outer"><div>
                <div class="filter_toggle">Фильтр</div>
                <div class="filter_block">
                    <div class="filter_row"><label><input type="checkbox" name="filter[only_managers]" value="1" <?=(empty($filter['only_managers']) ? '' : 'checked')?>> Только менеджеры</label></div>
                    <button class="btn">Применить</button>
                </div>
            </div></div>
        </form>

        <div class="tab_v tab_v_small"><div>
            <a href="#manager_add" class="fancy">Добавить менеджера</a>
        </div></div>

        <div class="scroll">
            <?if(empty($managers)){?>
                <div class="tab_v"><div>
                    <span class="gray">Менеджеры не найдены</span>
                </div></div>
            <?}else{?>
                <?foreach($managers as $key => $manager){?>
                    <div class="tab_v tab_v_small" tab="manager<?=$manager['MANAGER_ID']?>"><div>
                        <span class="gray">[<?=$manager['MANAGER_ID']?>]</span>
                        <?=$manager['M_NAME']?>
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

<?=$popupManagerAdd?>

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