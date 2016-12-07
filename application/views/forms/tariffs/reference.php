<div class="reference_block" uid="<?=$uid?>">
    <?
    $referenceList = [];

    foreach($reference as $referenceBlock){
        $referenceItem = reset($referenceBlock);
        $referenceList[] = [
            'CONDITION_ID' => $referenceItem['CONDITION_ID'],
            'WEB_CONDITION' => $referenceItem['WEB_CONDITION'],
        ];
    }
    ?>
    <select name="CONDITION_ID">
        <?foreach($referenceList as $referenceItem){?>
            <option value="<?=$referenceItem['CONDITION_ID']?>">
                <?=$referenceItem['WEB_CONDITION']?>
            </option>
        <?}?>
    </select>

    <?foreach($reference as $referenceBlock){
        $referenceFirst = reset($referenceBlock);?>
        <select class="reference_compare" name="COMPARE_ID" condition_id="<?=$referenceFirst['CONDITION_ID']?>">
            <?foreach($referenceBlock as $referenceItem){?>
                <option value="<?=$referenceItem['COMPARE_ID']?>">
                    <?=$referenceItem['WEB_COMPARISON']?>
                </option>
            <?}?>
        </select>
        <span class="web_form_element" condition_id="<?=$referenceFirst['CONDITION_ID']?>"><?=Common::buildFormField('tariffs', $referenceFirst['WEB_FORM'], $referenceFirst['WEB_FORM'])?></span>
    <?}?>
</div>

<script>
    $(function () {
        $('.reference_block[uid=<?=$uid?>] [name=CONDITION_ID]').on('change', function () {
            onChangeCondition($(this));
        });
    });
</script>