<div class="tabs_block">
    <div class="tabs">
        <?foreach($tabs as $tabId => $tab){
            if ($tabActive == $tabId) {
                ?><span class="tab <?=($tabActive == $tabId ? 'active' : '')?>"><i class="<?=$tab['icon']?>"></i> <?=$tab['name']?></span><?
            } else {
                ?><span onclick="loadSupplierContract('<?=$tabId?>')" class="tab"><i class="<?=$tab['icon']?>"></i> <?=$tab['name']?></span><?
            }
        }?>
    </div>
    <div class="tabs_content tabs_content_no_padding">
        <?=$content?>
    </div>
</div>