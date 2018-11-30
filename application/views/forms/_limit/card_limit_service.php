<div class="form_elem" limit_service>
    <nobr>
            <select name="limit_service" onchange="checkServices_<?=$postfix?>()" <?=(empty($settings['editServiceSelect']) ? 'disabled' : '')?>>
            <?foreach($servicesList as $service){?>
                <option
                        measure="<?=$service['MEASURE']?>"
                        group="<?=$service['SYSTEM_SERVICE_CATEGORY']?>"
                        value="<?=$service['SERVICE_ID']?>"
                        <?if(isset($limitService['id']) && $service['SERVICE_ID'] == $limitService['id']){?>selected<?}?>
                ><?=$service['FOREIGN_DESC']?></option>
            <?}?>
        </select>

        <?if ($settings['canDelService']) {?>
            <button class="btn btn_small btn_red btn_card_edit_del_serviсe" onclick="cardEditDelService_<?=$postfix?>($(this))">&times;</button>
        <?}?>
    </nobr>
</div>