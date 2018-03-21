<div class="popup_list_preview">
    <span class="btn btn_green">Загрузить фирмы</span>
</div>
<div class="popup_list"></div>
<div class="popup_list_btns">
    <span class="btn btn_reverse btn_add_firms_to_group_go">+ Добавить фирмы</span>
    <span class="btn btn_red pre_fancy_close">Отмена</span>
</div>

<script>
    $(function(){
        $('.pre_fancy_close').on('click', function () {
            $.fancybox.close();
            setTimeout(function () {
                $('.popup_list').empty().hide();
                $('.popup_list_preview').show();
            }, 500);
        });

        $('.popup_list_preview .btn').on('click', function () {
            var block = $('.popup_list');

            $('.popup_list_preview').hide();
            block.show().addClass('block_loading');
            setTimeout(function () {
                $.fancybox.update();
            }, 100);

            var groupId = $('.tabs_firms_groups .tab_v.active [name=group_id]').val();

            $.post('/control/show-firms', { postfix: 'popup_list', show_checkbox:1, group_id:groupId }, function (data) {
                block.removeClass('block_loading');

                block.html(data);
            });
        });

        $('.btn_add_firms_to_group_go').on('click', function () {
            var block = $('.popup_list');
            var groupId = $('.tabs_firms_groups .tab_v.active [name=group_id]').val();
            var posIds = [];

            $('[name=pos_id]:checked', block).each(function () {
                posIds.push($(this).val());
            });

            if(posIds.length == 0){
                message(0, 'Не выбрано ни одной фирмы');
                return false;
            }

            $.post('/control/add-firms-to-group', {pos_ids:posIds, group_id:groupId}, function (data) {
                if(data.success){
                    message(1, 'Фирмы успешно добавлены');

                    var tab = $('.tabs_firms_groups .tab_v.active');

                    loadGroupfirms(tab, true);
                }  else {
                    message(0, 'Ошибка добавления фирм');
                }
                $('.pre_fancy_close').click();
            });
        })
    });
</script>