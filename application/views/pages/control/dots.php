<h1>Точки обслуживания</h1>

<div class="tabs_block tabs_switcher tabs_group_dots">
    <div class="tabs">
        <span tab="groups" class="tab active">Группы точек</span><span tab="info" class="tab">Информация о точках</span>
    </div>
    <div class="tabs_content tabs_content_no_padding">
        <div tab_content="groups" class="tab_content active">
            <div class="tab_content_header">
                <div class="fr">
                    <a href="#" class="btn">Добавить группу</a>
                    <a href="#" class="btn">Добавить компанию</a>
                </div>
                <div class="input_with_icon"><i class="icon-find"></i><input type="text" name="filter[search]" class="input_big input_messages" placeholder="Поиск..." value=""></div>
            </div>

            <div class="tabs_vertical_block tabs_switcher">
                <div class="tabs_v">
                    <div class="before_scroll">
                        <div class="tab_v tab_v_small"><div>
                            <a href="#manager_add" class="fancy">+ Добавить группу</a>
                        </div></div>
                    </div>

                    <div class="scroll">
                        <?if(empty($dotsGroups)){?>
                            <div class="tab_v"><div>
                                <span class="gray">Группы не найдены</span>
                            </div></div>
                        <?}else{?>
                            <?foreach($dotsGroups as $key => $group){?>
                                <div class="tab_v tab_v_small" tab="group_dot<?=$group['GROUP_ID']?>"><div>
                                    <span class="gray">[<?=$group['GROUP_ID']?>]</span>
                                    <?=$group['GROUP_NAME']?>
                                </div></div>
                            <?}?>
                        <?}?>
                    </div>
                </div>
                <div class="tabs_v_content tabs_content_no_padding">
                    <?if(!empty($dotsGroups)){?>
                        <?foreach($dotsGroups as $key => $group){?>
                            <div class="tab_v_content" tab_content="group_dot<?=$group['GROUP_ID']?>"></div>
                        <?}?>
                    <?}?>
                </div>
            </div>

        </div>
        <div tab_content="info" class="tab_content"></div>
    </div>
</div>

<script>
    $(function(){
        $(".tabs_group_dots .tabs_v .scroll > [tab]").on('click', function(){
            var t = $(this);

            loadGroupDots(t);
        });

        $('.tabs_group_dots .tabs_v .scroll > [tab]:first').click();
    });

    function loadGroupDots(tab, force)
    {
        var tabsBlock = $(".tabs_group_dots");
        var groupId = tab.attr('tab').replace('group_dot', '');
        var tabContent = $("[tab_content=group_dot"+ groupId +"]", tabsBlock);

        if(tabContent.text() == '' || force == true){
            tabContent.empty().parent().addClass('block_loading');

            $.post('/control/group_dots/' + groupId, {}, function(data){
                tabContent.html(data).parent().removeClass('block_loading');
            });
        }
    }
</script>