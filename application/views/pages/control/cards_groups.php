<h1>Группы карт</h1>

<div class="block no_padding">
    <div class="tab_content_header">
        <div class="fr">
            <span toggle_block="cards_groups_block">
                <a href="#control_add_cards_group" class="btn fancy">+ Добавить группу</a>
                <a href="#control_add_cards" class="btn fancy">+ Добавить карты</a>
                <span class="btn btn_green btn_icon" toggle="cards_groups_block"><i class="icon-pen"></i></span>
            </span>

            <span toggle_block="cards_groups_block" class="dn action_del">
                <a href="#" class="btn btn_red btn_del_cards_groups"><i class="icon-cancel"></i> Удалить выделенные группы</a>
                <a href="#" class="btn btn_red btn_del_cards"><i class="icon-cancel"></i> Удалить выделенные карты</a>
                <span class="btn btn_orange btn_icon" toggle="cards_groups_block"><i class="icon-cancel"></i></span>
            </span>
        </div>
        <form class="form_cards_groups" onsubmit="return collectForms($(this), 'form_cards_groups')">
            <div class="input_with_icon"><i class="icon-find"></i><input type="text" name="filter[search]" class="input_big input_messages" placeholder="Поиск..." value="<?=(!empty($filter['search']) ? $filter['search'] : '')?>"></div>
        </form>
    </div>

    <div class="tabs_vertical_block tabs_switcher tabs_cards_groups">
        <div class="tabs_v tabs_v_cards_groups check_box_active_reverse">
            <div class="scroll">
                <?if(empty($cardsGroups)){?>
                    <div class="tab_v"><div>
                        <span class="gray">Группы не найдены</span>
                    </div></div>
                <?}else{?>
                    <?foreach($cardsGroups as $key => $group){?>
                        <div class="tab_v tab_v_small" tab="cards_group_<?=$group['GROUP_ID']?>"><div>
                            <?if(in_array($user['role'], Access::$adminRoles)){?>
                                <span class="check_span_hidden">
                                    <input type="checkbox" name="group_id" value="<?=$group['GROUP_ID']?>">
                                    <input type="hidden" name="group_name" value="<?=$group['GROUP_NAME']?>">

                                    <span class="btn btn_green btn_tiny btn_icon" onclick="showEditCardsGroupPopup(<?=$group['GROUP_ID']?>)"><i class="icon-pen"></i></span>
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
            <?if(!empty($cardsGroups)){?>
                <?foreach($cardsGroups as $key => $group){?>
                    <div class="tab_v_content" tab_content="cards_group_<?=$group['GROUP_ID']?>"></div>
                <?}?>
            <?}?>
        </div>
    </div>
</div>

<?=$popupAddCards?>
<?=$popupAddCardsGroup?>
<?=$popupEditCardsGroup?>

<script>
    $(function(){
        $(".tabs_cards_groups .tabs_v .scroll > [tab]").on('click', function(){
            var t = $(this);

            loadGroupCards(t);
        });

        $('.tabs_cards_groups .tabs_v .scroll > [tab]:first').click();

        $("[toggle=cards_groups_block]").on('click', function(){
            $('.check_span_hidden, .td_check, .td_edit').toggle();
        });

        $('.btn_del_cards').on('click', function () {
            var dots = [];

            $('.td_check [type=checkbox][name=pos_id]:checked').each(function () {
                dots.push($(this).val());
            });

            if(dots.length == 0){
                message(0, 'Не выделенно ни одной карты');
            }

            if(!confirm('Удалить ' + dots.length + ' точки?')){
                return false;
            }
        });
        $('.btn_del_cards_groups').on('click', function () {
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

            if(!confirm('Удалить ' + groups.length + ' групп карт?')){
                return false;
            }

            $.post('/control/del_cards_group', {groups: groups}, function (data) {

                for(var i in data.data.deleted){
                    selectedGroups['group' + i].remove();
                }

                for(var i in data.data.not_deleted){
                    message(0, 'Ошибка удаления. Группа <b>'+ selectedGroups['group' + i].find('.group_name').text() +'</b> содержит карты');
                }

                $('.tabs_cards_groups .tabs_v .scroll > [tab]:first').click();

            });
        });
    });

    function loadGroupCards(tab, force)
    {
        var tabsBlock = $(".tabs_cards_groups");
        var groupId = tab.attr('tab').replace('cards_group_', '');
        var tabContent = $("[tab_content=cards_group_"+ groupId +"]", tabsBlock);

        if(tabContent.text() == '' || force == true){
            tabContent.empty().parent().addClass('block_loading');

            $.post('/control/load_group_cards/' + groupId, {}, function(data){
                tabContent.html(data).parent().removeClass('block_loading');
            });
        }
    }

    function showEditCardsGroupPopup(groupId)
    {
        var block = $('#control_edit_cards_group');

        $('input', block).val('');

        $('[name=edit_cards_group_name]', block).val($('[tab=cards_group_'+ groupId +'] [name=group_name]').val());
        $('[name=edit_cards_group_id]', block).val(groupId);

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