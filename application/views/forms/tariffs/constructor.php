<div class="tariff_wrapper">
    <table>
        <tr>
            <td class="gray right" width="200">Название:</td>
            <td>
                <input type="hidden" name="tarif_id" value="<?=(!empty($tariff['TARIF_ID']) ? $tariff['TARIF_ID'] : 0)?>">
                <input type="text" name="tarif_name" class="input_big input_grand" value="<?=(!empty($tariff['TARIF_NAME']) ? Text::quotesForForms($tariff['TARIF_NAME']) : '')?>">
            </td>
        </tr>
        <tr>
            <td class="gray right">Версия:</td>
            <td>
                <select name="tariff_versions">
                    <?foreach ($tariff['versions'] as $version) {?>
                        <option value="<?=$version['VERSION_ID']?>" <?=($version['VERSION_ID'] == $tariff['current_version'] ? 'selected' : '')?>>
                            <?=$version['VERSION_ID']?> от <?=$version['DATE_CREATE_STR']?>
                        </option>
                    <?}?>
                </select>
                <span class="btn btn_small btn_green btn_reverse" onclick="loadTariffVersion($(this))">Посмотреть</span>
            </td>
        </tr>
    </table>

    <div class="t_sections_list">
        <?if(!empty($settings)){?>
            <?foreach($settings as $conditions){
                $section = reset($conditions);
                $uidSection = $tariff['TARIF_ID'].'_'.$section['SECTION_NUM'];
                ?>
                <?=Model_Tariff::buildSection($uidSection, $section,  $tariff['TARIF_ID'], $conditions, $reference)?>
            <?}?>
        <?}?>
    </div>

    <?if ($tariff['current_version'] == $tariff['LAST_VERSION']) {?>
    <span class="btn btn_add_section" onclick="addSection($(this))">+ Добавить секцию</span>

    <div class="row_btns">
        <span class="btn btn_green" onclick="saveTariff($(this))"><i class="icon-ok"></i> Сохранить</span>
    </div>
    <?} else {?>
        <i class="gray">Только просмотр</i>
    <?}?>
</div>