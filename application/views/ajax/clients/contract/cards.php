<?
$cntWork = 0;
$cntDisable = 0;

foreach($cards as $card){
    if($card['CARD_STATE'] != Model_Card::CARD_STATE_BLOCKED){
        $cntWork++;
    }else{
        $cntDisable++;
    }
}
?>

<div class="tc_top_line">
    <span class="gray">Всего карт:</span> <?=count($cards)?> &nbsp;&nbsp;&nbsp;
    <span class="gray">В работе:</span> <?=$cntWork?> &nbsp;&nbsp;&nbsp;
    <span class="red">Заблокировано: <?=$cntDisable?></span>
    <div class="fr input_with_icon"><i class="icon-find"></i><input type="text" class="input_big cards_search" placeholder="Поиск..." value="<?=$query?>"></div>
</div>
<div class="tabs_vertical_block tabs_switcher tabs_cards">
    <div class="tabs_v">
        <?if(Access::allow('add_card')){?>
            <div class="tab_v"><div>
                <a href="#card_add" class="fancy"><span class="icon-card"></span> Добавить карту</a>
            </div></div>
        <?}?>
        <div class="scroll">
            <?foreach($cards as $key => $card){?>
                <div class="tab_v" tab="<?=$card['CARD_ID']?>"><div>
                    <span class="icon-card gray"></span>
                    <?=$card['CARD_ID']?>
                    <div class="gray"><?=$card['HOLDER']?></div>
                    <?if($card['CARD_STATE'] == Model_Card::CARD_STATE_BLOCKED){?>
                        <span class="label label_error label_small">Заблокирована</span>
                    <?}?>
                </div></div>
            <?}?>
        </div>
        <!--div class="tab_v gray preload"><div>
                <span class="icon-loader"></span> Загрузка карточек
            </div></div-->
    </div>
    <div class="tabs_v_content">
        <?foreach($cards as $key => $card){?>
            <div class="tab_v_content" tab_content="<?=$card['CARD_ID']?>"></div>
        <?}?>
    </div>
</div>

<?if(Access::allow('add_card')){?>
    <?=$popupCardAdd?>
<?}?>

<script>
    $(function(){
        $(".tabs_cards [tab]").on('click', function(){
            var t = $(this);

            if($(".tabs_cards [tab_content="+ t.attr('tab') +"]").text() == ''){
                $(".tabs_cards [tab_content="+ t.attr('tab') +"]").addClass('block_loading');

                $.post('/clients/card/' + t.attr('tab'), {}, function(data){
                    $(".tabs_cards [tab_content="+ t.attr('tab') +"]").html(data).removeClass('block_loading');
                });
            }
        });

        $(".tabs_cards [tab]:first").click();

        $(".cards_search").on('keypress', function(e){
            if(e.keyCode == 13){
                loadContract('cards', $(".cards_search").val());
            }
        });

        <?if(Access::allow('add_card')){?>
            renderScroll($('.tabs_cards .scroll'), -70);
        <?}else{?>
            renderScroll($('.tabs_cards .scroll'));
        <?}?>
    });
</script>