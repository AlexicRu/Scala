<h1>Тарифы</h1>

<?if(empty($tariffs)){?>
    <div class="error_block">Нет доступных тарифов</div>
<?}else{?>

    <div class="tabs_vertical_block tabs_switcher tabs_tariffs">
        <div class="tabs_v">
            <?foreach($tariffs as $tariff){?>
                <div class="tab_v tab_v_small" tab="<?=$tariff['TARIF_ID']?>" version="<?=$tariff['LAST_VERSION']?>">
                    <div>
                        <a href="#"><?=$tariff['TARIF_NAME']?></a>
                    </div>
                </div>
            <?}?>
        </div>
        <div class="tabs_v_content">
            <?foreach($tariffs as $tariff){?>
                <div class="tab_v_content tariffs_block" tab_content="<?=$tariff['TARIF_ID']?>"></div>
            <?}?>
        </div>
    </div>

<?}?>

<script>
    $(function(){
        $(".tabs_tariffs [tab]").on('click', function(){
            var t = $(this);
            var tab = t.attr('tab');

            loadTariff(tab);
        });

        var clicked = false;
        $(".tabs_tariffs [tab]").each(function(){
            if(clicked){
                return;
            }
            var t = $(this);

            if(!t.attr('style')){
                clicked = true;
                t.click();
            }
        });
    });

    function loadTariff(tariff, force)
    {
        var block = $('.tariffs_block[tab_content='+ tariff +']');

        if(block.text() == '' || force == true){
            block.empty().addClass('block_loading');

            $.post('/control/load_tariff/' + tariff, { version: $('[tab='+ tariff +']').attr('version') }, function(data){
                block.html(data).removeClass('block_loading');
            });
        }
    }
</script>