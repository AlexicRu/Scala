<h1>Группы фирм</h1>

<div class="block no_padding">
    <div class="tab_content_header">
        <div class="fr">
            <span toggle_block="firms_groups_block">
                <a href="#control_add_firms_group" class="btn fancy">+ Добавить группу</a>
                <a href="#control_add_firms" class="btn fancy">+ Добавить карты</a>
                <span class="btn btn_green btn_icon" onclick="groupFirmsToXls()"><i class="icon-exel1"></i> Выгрузить</span>
                <span class="btn btn_green btn_icon" toggle="firms_groups_block"><i class="icon-pen"></i></span>
            </span>

            <span toggle_block="firms_groups_block" class="dn action_del">
                <a href="#" class="btn btn_red btn_del_firms_groups"><i class="icon-cancel"></i> Удалить выделенные группы</a>
                <a href="#" class="btn btn_red btn_del_firms"><i class="icon-cancel"></i> Удалить выделенные фирмы</a>
                <span class="btn btn_orange btn_icon" toggle="firms_groups_block"><i class="icon-cancel"></i></span>
            </span>
        </div>
        <form class="form_firms_groups" onsubmit="return collectForms($(this), 'form_firms_groups')">
            <div class="input_with_icon"><i class="icon-find"></i><input type="text" name="filter[search]" class="input_big input_messages" placeholder="Поиск..." value="<?=(!empty($filter['search']) ? $filter['search'] : '')?>"></div>
        </form>
    </div>

    <div class="tabs_vertical_block tabs_switcher tabs_firms_groups">
        <div class="tabs_v tabs_v_groups check_box_active_reverse">
            <div class="scroll">
                <?if(empty($firmsGroups)){?>
                    <div class="tab_v"><div>
                        <span class="gray">Группы не найдены</span>
                    </div></div>
                <?}else{?>
                    <?foreach($firmsGroups as $key => $group){?>
                        <div class="tab_v tab_v_small" tab="firms_group_<?=$group['GROUP_ID']?>"><div>
                            <?/*if(in_array($user['role'], Access::$adminRoles)){*/?>
                                <span class="check_span_hidden">
                                    <input type="checkbox" name="group_id" value="<?=$group['GROUP_ID']?>">
                                    <input type="hidden" name="group_name" value="<?=$group['GROUP_NAME']?>">

                                    <span class="btn btn_green btn_tiny btn_icon" onclick="showEditFirmsGroupPopup(<?=$group['GROUP_ID']?>)"><i class="icon-pen"></i></span>
                                </span>
                            <?/*}*/?>

                            <span class="gray">[<?=$group['GROUP_ID']?>]</span>
                            <span class="group_name"><?=$group['GROUP_NAME']?></span>
                        </div></div>
                    <?}?>
                <?}?>
            </div>
        </div>
        <div class="tabs_v_content tabs_content_no_padding">
            <?if(!empty($groups)){?>
                <?foreach($groups as $key => $group){?>
                    <div class="tab_v_content" tab_content="firms_group_<?=$group['GROUP_ID']?>" group_id="<?=$group['GROUP_ID']?>"></div>
                <?}?>
            <?}?>
        </div>
    </div>
</div>

<?=$popupAddFirms?>
<?=$popupAddFirmsGroup?>
<?=$popupEditFirmsGroup?>

<script>
    $(function(){
        $(".tabs_firms_groups .tabs_v .scroll > [tab]").on('click', function(){
            var t = $(this);

            loadGroupFirms(t);
        });

        $('.tabs_firms_groups .tabs_v .scroll > [tab]:first').click();

        $("[toggle=firms_groups_block]").on('click', function(){
            $('.check_span_hidden, .td_check, .td_edit').toggle();
        });

        $('.btn_del_firms').on('click', function () {
            var firms = [];
            var group = $('.tab_v_content.active');
            var group_id = group.attr('group_id');

            $('.td_check [type=checkbox][name=firm_id]:checked').each(function () {
                firms.push($(this).val());
            });

            if(firms.length == 0){
                message(0, 'Не выделенно ни одной фирмы');
            }

            if(!confirm('Удалить ' + firms.length + ' фирмы?')){
                return false;
            }

            $.post('/control/del-firms-from-group', {group_id: group_id, firms_numbers: firms}, function (data) {
                if (data.success) {
                    message(1, 'Фирмы удалены');

                    for(var i in firms){
                        $('.firm_row[id="'+ firms[i] +'"]', group).remove();
                    }
                } else {
                    message(0, 'Ошибка удаления');
                }
            });
        });

        $('.btn_del_firms_groups').on('click', function () {
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

            if(!confirm('Удалить ' + groups.length + ' групп(ы) фирм?')){
                return false;
            }

            $.post('/control/del-firms-group', {groups: groups}, function (data) {

                for(var i in data.data.deleted){
                    selectedGroups['group' + i].remove();
                }

                for(var i in data.data.not_deleted){
                    message(0, 'Ошибка удаления. Группа <b>'+ selectedGroups['group' + i].find('.group_name').text() +'</b> содержит фирмы');
                }

                $('.tabs_firms_groups .tabs_v .scroll > [tab]:first').click();

            });
        });
    });

    function loadGroupFirms(tab, force)
    {
        var tabsBlock = $(".tabs_firms_groups");
        var groupId = tab.attr('tab').replace('firms_group_', '');
        var tabContent = $("[tab_content=firms_group_"+ groupId +"]", tabsBlock);

        if(tabContent.text() == '' || force == true){
            tabContent.empty().parent().addClass(CLASS_LOADING);

            $.post('/control/load-group-firms/' + groupId, {}, function(data){
                tabContent.html(data).parent().removeClass(CLASS_LOADING);
            });
        }
    }

    function showEditFirmsGroupPopup(groupId)
    {
        var block = $('#control_edit_firms_group');

        $('input', block).val('');

        $('[name=edit_firms_group_name]', block).val($('[tab=firms_group_'+ groupId +'] [name=group_name]').val());
        $('[name=edit_firms_group_id]', block).val(groupId);

        $.fancybox.open(block, {
            padding: [0,0,0,0]
        });
    }

    function groupFirmsToXls()
    {
        var group = $(".tabs_firms_groups .tab_v[tab].active");

        if (group.length == 0) {
            message(0, 'Нет данный для выгрузки');
            return;
        }

        var group_id = group.attr('tab').replace('firms_group_', '');
        window.open('/control/load-group-firms/?group_id=' + group_id + '&to_xls=1');
    }
</script>