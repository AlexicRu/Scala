<fieldset uid_section="<?=$uidSection?>" section_num="<?=$section['SECTION_NUM']?>">
    <legend>Секция <?=$section['SECTION_NUM']?></legend>
    <span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span>

    <div class="ts_conditions">
        <?foreach($conditions as $condition){
            $uid = $tariffId.'_'.$section['SECTION_NUM'].'_'.$condition['CONDITION_ID'];
            ?>
            <div class="tsc_item line_inner">
                <span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span>

                <div class="line_inner_100">Условие:</div>
                <?=Model_Tariff::buildReference($uid, $reference)?>
                <script>
                    $(function () {
                        changeCondition('<?=$uid?>', <?=$condition['CONDITION_ID']?>, <?=$condition['COMPARE_ID']?>, <?=$condition['CONDITION_VALUE']?>);
                    });
                </script>
            </div>
        <?}?>
    </div>

    <span class="btn btn_add_condition btn_small" onclick="addSectionCondition($(this))">+ Добавить условие</span>

    <br><br>

    <b class="f18">Параметры:</b>
    <?=Model_Tariff::buildParams($uidSection, (!empty($section['params']) ? $section['params'] : []))?>
</fieldset>