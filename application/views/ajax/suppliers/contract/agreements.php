<div class="tabs_vertical_block tabs_switcher tabs_agreements">
    <div class="tabs_v">
        <?if(Access::allow('suppliers_agreement_add')){?>
            <div class="before_scroll">
                <div class="tab_v"><div>
                        <a href="#supplier_agreement_add" class="fancy"><span class="icon-card"></span> Добавить соглашение</a>
                    </div></div>
            </div>
        <?}?>
        <div class="scroll">
            <?foreach($agreements as $key => $agreement){?>
                <div class="tab_v" tab="<?=$agreement['AGREEMENT_ID']?>" onclick="loadAgreement($(this))">
                    <div>
                        <span class="icon-card gray"></span>
                        [<?=$agreement['AGREEMENT_ID']?>] <span class="agreement_name"><?=$agreement['AGREEMENT_NAME']?></span>
                    </div>
                </div>
            <?}?>
        </div>
    </div>
    <div class="tabs_v_content tabs_content_no_padding">
        <?foreach($agreements as $key => $agreement){?>
            <div class="tab_v_content" tab_content="<?=$agreement['AGREEMENT_ID']?>"></div>
        <?}?>
    </div>
</div>

<?if(Access::allow('suppliers_agreement_add')){?>
    <?=$popupAgreementAdd?>
<?}?>

<script>
    $(function(){
        var clicked = false;
        $(".tabs_agreements [tab]").each(function(){
            if(clicked){
                return;
            }

            clicked = true;
            $(this).click();
        });
    });
</script>