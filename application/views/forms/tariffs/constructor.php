<table>
    <tr>
        <td class="gray right" width="200">Название:</td>
        <td>
            <input type="text" class="input_big" value="<?=$tariff['TARIF_NAME']?>">
        </td>
    </tr>
</table>

<?if(!empty($settings)){?>
    <div class="t_sections_list">
        <?foreach($settings as $conditions){
            $section = reset($conditions);
            $uidSection = $tariff['TARIF_ID'].'_'.$section['SECTION_NUM'];
            ?>
            <fieldset uid_section="<?=$uidSection?>">
                <legend>Секция <?=$section['SECTION_NUM']?></legend>
                <span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span>

                <div class="ts_conditions">
                    <?foreach($conditions as $condition){
                        $uid = $tariff['TARIF_ID'].'_'.$section['SECTION_NUM'].'_'.$condition['CONDITION_ID'];
                        ?>
                        <div class="tsc_item line_inner">
                            <span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span>

                            <div class="line_inner_100">Условие:</div>
                            <?=Common::buildReference($uid, $reference)?>
                            <script>
                                $(function () {
                                    changeCondition('<?=$uid?>', <?=$condition['CONDITION_ID']?>, <?=$condition['COMPARE_ID']?>);
                                });
                            </script>
                        </div>
                    <?}?>
                </div>

                <span class="btn btn_add_condition btn_small" onclick="addSectionCondition($(this))">+ Добавить условие</span>

                <br><br>

                <b class="f18">Параметры:</b>
                <table>
                    <tr>
                        <td class="gray right" width="100">Тип:</td>
                        <td>
                            <select>
                                <option>тип 1</option>
                            </select>
                        </td>
                        <td class="gray right" width="100">Параметр:</td>
                        <td>
                            <select>
                                <option>параметр 1</option>
                            </select>
                        </td>
                        <td class="gray right" width="100">Размер:</td>
                        <td>
                            <input type="text">
                        </td>
                    </tr>
                </table>
            </fieldset>
        <?}?>
    </div>
<?}?>
<span class="btn btn_add_section">+ Добавить секцию</span>

<div class="row_btns">
    <span class="btn btn_green"><i class="icon-ok"></i> Сохранить</span>
</div>


<script>
    $(function(){
        $('.btn_add_section').on('click', function () {
            var t = $(this);
            var block = t.closest('.tariffs_block');
            var list = block.find('.t_sections_list');

            var tpl = $('<fieldset><legend>Секция</legend><span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span></fieldset>');

            tpl.appendTo(list);
        });

        $(document).on('click', '.ts_remove', function () {
            if(!confirm('Удаляем?')) {
                return;
            }

            var t = $(this);
            var fieldset = t.closest('fieldset, .tsc_item');
            var uidSection;
            var btnAddCondition;

            if(t.closest('.tsc_item').length){
                uidSection = t.closest('[uid_section]');
                btnAddCondition = uidSection.find('.btn_add_condition');
            }

            fieldset.remove();

            if(uidSection) {
                btnAddCondition.show();
                checkUsedConditions(uidSection.attr('uid_section'));
            }
        });
    });
</script>