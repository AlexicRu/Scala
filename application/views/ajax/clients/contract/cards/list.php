<div class="ajax_block_cards_list_out block_loading">

</div>

<script>
    $(function () {
        paginationAjax('/clients/cards_list/?contract_id=' + $('[name=contracts_list]').val(), 'ajax_block_cards_list', renderAjaxPaginationCardsList);

        $(".cards_search").on('keypress', function(e){
            if(e.keyCode == 13){
                $('.ajax_block_cards_list_out').empty().addClass('block_loading')
                    .closest('.tabs_cards').find('.tabs_v_content').empty()
                ;

                var params = {
                    query: $(this).val()
                };

                paginationAjax('/clients/cards_list/?contract_id=' + $('[name=contracts_list]').val(), 'ajax_block_cards_list', renderAjaxPaginationCardsList, params);
            }
        });
    });

    function renderAjaxPaginationCardsList(data, block)
    {
        if (data.length == 0) {
            block.empty().append('<div class="tab_v"><div><span class="gray">Карты не найдены</span></div></div>');
        } else {
            var firstLoad = block.find('tab_v').length;

            var contentBlock = block.closest('.tabs_cards').find('.tabs_v_content');

            for (var i in data) {
                var tpl = $('<div class="tab_v" onclick="cardLoad($(this))"><div><span class="icon-card gray"></span><span card_id /><div class="gray" holder /></div></div>');

                tpl.attr('tab', data[i].CARD_ID);
                tpl.find('[card_id]').text(data[i].CARD_ID);
                tpl.find('[holder]').text(data[i].HOLDER)

                if (data[i].CARD_STATE == <?=Model_Card::CARD_STATE_BLOCKED?>) {
                    tpl.find('> div').append('<span class="label label_error label_small">Заблокирована</span>');
                }

                tpl.appendTo(block);

                contentBlock.append('<div class="tab_v_content" tab_content="'+ data[i].CARD_ID +'"></div>');
            }

            if (!firstLoad) {
                block.find('.tab_v[tab]:first').click();
            }
        }
    }
</script>