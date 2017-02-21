<h1>Точки обслуживания</h1>

<div class="tabs_block tabs_switcher tabs_group_dots">
    <div class="tabs">
        <span tab="groups" class="tab active">Группы точек</span><span tab="info" class="tab" onclick="showDotsList()">Информация о точках</span>
    </div>
    <div class="tabs_content tabs_content_no_padding">
        <div tab_content="groups" class="tab_content active">
            <div class="tab_content_header">
                <div class="fr">
                    <span toggle_block="group_dots_block">
                        <a href="#control_add_dots_group" class="btn fancy">+ Добавить группу</a>
                        <a href="#control_add_dot" class="btn fancy">+ Добавить точки</a>
                        <span class="btn btn_green btn_icon" toggle="group_dots_block"><i class="icon-pen"></i></span>
                    </span>

                    <span toggle_block="group_dots_block" class="dn action_del">
                        <a href="#" class="btn btn_red btn_del_dots_groups"><i class="icon-cancel"></i> Удалить выделенные группы</a>
                        <a href="#" class="btn btn_red btn_del_dots"><i class="icon-cancel"></i> Удалить выделенные точки</a>
                        <span class="btn btn_orange btn_icon" toggle="group_dots_block"><i class="icon-cancel"></i></span>
                    </span>
                </div>
                <form>
                    <div class="input_with_icon"><i class="icon-find"></i><input type="text" name="filter[search]" class="input_big input_messages" placeholder="Поиск..." value="<?=(!empty($filter['search']) ? $filter['search'] : '')?>"></div>
                </form>
            </div>

            <div class="tabs_vertical_block tabs_switcher">
                <div class="tabs_v tabs_v_dots check_box_active_reverse">
                    <div class="scroll">
                        <?if(empty($dotsGroups)){?>
                            <div class="tab_v"><div>
                                <span class="gray">Группы не найдены</span>
                            </div></div>
                        <?}else{?>
                            <?foreach($dotsGroups as $key => $group){?>
                                <div class="tab_v tab_v_small" tab="group_dot<?=$group['GROUP_ID']?>"><div>
                                    <?if($group['GROUP_TYPE'] != Model_Dot::GROUP_TYPE_SUPPLIER || in_array($user['role'], Access::$adminRoles)){?>
                                        <span class="check_span">
                                            <input type="checkbox" name="group_id" value="<?=$group['GROUP_ID']?>">
                                            <input type="hidden" name="group_name" value="<?=$group['GROUP_NAME']?>">
                                            <input type="hidden" name="group_type" value="<?=$group['GROUP_TYPE']?>">

                                            <span class="btn btn_green btn_tiny btn_icon" onclick="showEditDotsGroupPopup(<?=$group['GROUP_ID']?>)"><i class="icon-pen"></i></span>
                                        </span>
                                    <?}?>

                                    <span class="gray">[<?=$group['GROUP_ID']?>]</span>
                                    <span class="group_name"><?=$group['GROUP_NAME']?></span>
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

<?=$popupEditDotsGroup?>
<?=$popupAddDotsGroup?>
<?=$popupAddDot?>

<script>
    $(function(){
        $(".tabs_group_dots .tabs_v .scroll > [tab]").on('click', function(){
            var t = $(this);

            loadGroupDots(t);
        });

        $('.tabs_group_dots .tabs_v .scroll > [tab]:first').click();

        $("[toggle=group_dots_block]").on('click', function(){
            $('.check_span, .dot_td_check, .dot_td_edit').toggle();
        });

        $('.btn_del_dots_dots').on('click', function () {
            var dots = [];

            $('.dot_td_check [type=checkbox][name=pos_id]:checked').each(function () {
                dots.push($(this).val());
            });

            if(dots.length == 0){
                message(0, 'Не выделенно ни одной точки');
            }

            if(!confirm('Удалить ' + dots.length + ' точки?')){
                return false;
            }
        });
        $('.btn_del_dots_groups').on('click', function () {
            var groups = [];
            var selectedGroups = {};

            $('[type=checkbox][name=group_id]:checked').each(function () {
                var t = $(this);
                groups.push(t.val());
                selectedGroups['group' + t.val()] = t.closest('.tab_v');
            });

            if(groups.length == 0){
                message(0, 'Не выделенно ни одной группы');
            }

            if(!confirm('Удалить ' + groups.length + ' групп точек?')){
                return false;
            }

            $.post('/control/del_group_dots', {groups: groups}, function (data) {

                for(var i in data.data.deleted){
                    selectedGroups['group' + i].remove();
                }

                for(var i in data.data.not_deleted){
                    message(0, 'Ошибка удаления. Группа <b>'+ selectedGroups['group' + i].find('.group_name').text() +'</b> содержит точки');
                }

                $('.tabs_group_dots .tabs_v .scroll > [tab]:first').click();

            });
        });
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

    function showEditDotsGroupPopup(groupId)
    {
        var block = $('#control_edit_dots_group');

        $('input', block).val('');

        $('[name=edit_dots_group_name]', block).val($('[tab=group_dot'+ groupId +'] [name=group_name]').val());
        $('[name=edit_dots_group_type]', block).val($('[tab=group_dot'+ groupId +'] [name=group_type]').val());
        $('[name=edit_dots_group_id]', block).val(groupId);

        $.fancybox.open(block, {
            padding: [0,0,0,0]
        });
    }

    function showDotsList()
    {
        var block = $('[tab_content=info]');

        if(block.html() != ''){
            return true;
        }

        block.addClass('block_loading');

        $.post('/control/show_dots', {  }, function (data) {
            block.removeClass('block_loading');

            block.html(data);
        });
    }
</script>