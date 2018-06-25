<h1>Тарифы</h1>

<div class="tabs_vertical_block tabs_switcher tabs_tariffs">
    <div class="tabs_v">
        <div class="before_scroll">
            <form>
                <div class="tab_v tab_v_small"><div>
                        <div class="input_with_icon"><i class="icon-find"></i><input type="text" name="filter[search]" class="input_big input_messages" placeholder="Поиск..." value="<?=(empty($filter['search']) ? '' : $filter['search'])?>"></div>
                    </div></div>
                <?/*?><div class="tab_v tab_v_filter filter_outer"><div>
                        <div class="filter_toggle">Фильтр</div>
                        <div class="filter_block">
                            <div class="filter_row"><label><input type="checkbox" name="filter[only_managers]" value="1" <?=(empty($filter['only_managers']) ? '' : 'checked')?>> Только менеджеры</label></div>
                            <button class="btn">Применить</button>
                        </div>
                    </div></div><?*/?>
            </form>
        </div>
        <div class="scroll">
            <div class="tab_v tab_v_small" tab="-1"><div>
                    <a href="#">Добавить тариф</a>
                </div></div>
            <?foreach($tariffs as $tariff){?>
                <div class="tab_v tab_v_small" tab="<?=$tariff['TARIF_ID']?>" version="<?=$tariff['LAST_VERSION']?>">
                    <div>
                        <a href="#">[<?=$tariff['TARIF_ID']?>] <?=$tariff['TARIF_NAME']?></a>
                    </div>
                </div>
            <?}?>
        </div>
    </div>
    <div class="tabs_v_content">
        <div class="tab_v_content tariffs_block" tab_content="-1"></div>
        <?foreach($tariffs as $tariff){?>
            <div class="tab_v_content tariffs_block" tab_content="<?=$tariff['TARIF_ID']?>"></div>
        <?}?>
    </div>
</div>

<script>
    $(function(){
        $(".tabs_tariffs [tab]").on('click', function(){
            var t = $(this);
            var tab = t.attr('tab');

            loadTariff(tab);
        });

        var clicked = false;
        $(".tabs_tariffs [tab]").each(function(){
            var t = $(this);

            if(t.attr('tab') != -1 || $(".tabs_tariffs [tab]").length == 1){
                if(clicked){
                    return;
                }

                if(!t.attr('style')){
                    clicked = true;
                    t.click();
                }
            }
        });
    });

    function loadTariff(tariff, force)
    {
        var block = $('.tariffs_block[tab_content='+ tariff +']');

        if(block.text() == '' || force == true){
            block.empty().addClass('block_loading');

            $.post('/control/load-tariff/' + tariff, { version: $('[tab='+ tariff +']').attr('version') }, function(data){
                block.html(data).removeClass('block_loading');
            });
        }
    }
</script>