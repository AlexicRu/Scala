<table>
    <tr>
        <td class="gray right" width="200">Название:</td>
        <td>
            <input type="hidden" name="tarif_id" value="<?=$tariff['TARIF_ID']?>">
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
            <?=Model_Tariff::buildSection($uidSection, $section,  $tariff['TARIF_ID'], $conditions, $reference)?>
        <?}?>
    </div>
<?}?>
<span class="btn btn_add_section" onclick="addSection($(this))">+ Добавить секцию</span>

<div class="row_btns">
    <span class="btn btn_green"><i class="icon-ok"></i> Сохранить</span>
</div>