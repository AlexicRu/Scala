<div class="ajax_block_cards_list_out block_loading">

</div>

<script>
    var findCard = getUrlParameter('card');
    var foundCard = false;
    $(function () {
        paginationAjax('/clients/cards-list/?contract_id=' + $('[name=contracts_list]').val(), 'ajax_block_cards_list', renderAjaxPaginationCardsList);

        $(".cards_search").on('keypress', function(e){
            if(e.keyCode == 13){
                var params = {
                    query: $(this).val()
                };

                reLoad(params);
            }
        });
    });

    function reLoad(params)
    {
        $('.ajax_block_cards_list_out').empty().addClass('block_loading')
            .closest('.tabs_cards').find('.tabs_v_content').empty()
        ;

        paginationAjax('/clients/cards-list/?contract_id=' + $('[name=contracts_list]').val(), 'ajax_block_cards_list', renderAjaxPaginationCardsList, params);
    }

    /**
     * фильтр
     */
    function filterCards(type)
    {
        var params = {
            query: $(".cards_search").val()
        };

        switch (type) {
            case 'work':
                params.status = 'work';
                break;
            case 'disabled':
                params.status = 'disabled';
                break;
        }

        reLoad(params);

        return false;
    }

    var cardsIcons = {};

    <?foreach (Model_Card::$cardIcons as $template => $icon) {?>
        cardsIcons['<?=$template?>'] = '<?=$icon?>';
    <?}?>

    function renderAjaxPaginationCardsList(data, block)
    {
        if (data.length == 0) {
            block.empty().append('<div class="tab_v"><div><span class="gray">Карты не найдены</span></div></div>');
        } else {
            var firstLoad = block.find('tab_v').length;

            var contentBlock = block.closest('.tabs_cards').find('.tabs_v_content');

            for (var i in data) {
                var tpl = $('<div class="tab_v" onclick="cardLoad($(this))"><div><span card_id /><div class="gray" holder /></div></div>');

                if (cardsIcons[data[i].CARD_TEMPLATE]) {
                    tpl.find('> div').prepend('<span class="card__picture" style="background-image: url(<?=Common::getAssetsLink()?>img/cards/'+ cardsIcons[data[i].CARD_TEMPLATE] +')"></span>');
                } else {
                    tpl.find('> div').prepend('<span class="icon-card gray"></span>');
                }

                tpl.attr('tab', data[i].CARD_ID);
                tpl.find('[card_id]').text(data[i].CARD_ID);
                tpl.find('[holder]').text(data[i].HOLDER)

                if (data[i].CARD_STATE == <?=Model_Card::CARD_STATE_BLOCKED?>) {
                    tpl.find('> div').append('<span class="label label_error"><i class="icon-block"></i></span>');
                }

                tpl.appendTo(block);

                contentBlock.append('<div class="tab_v_content" tab_content="'+ data[i].CARD_ID +'"></div>');

                if (findCard == data[i].CARD_ID) {
                    foundCard = true;
                }
            }

            if (findCard) {
                if (!foundCard) {
                    block.parent().find('.ajax_block_load').click();
                } else {
                    block.find('.tab_v[tab="'+ findCard +'"]').click();
                }
            } else if (!firstLoad) {
                block.find('.tab_v[tab]:first').click();
            }
        }
    }
</script>