<?
if(!empty($fields[Model_Report::REPORT_CONSTRUCTOR_TYPE_PERIOD])){
    ?><table><?
    foreach($fields[Model_Report::REPORT_CONSTRUCTOR_TYPE_PERIOD] as $field){?>
        <tr>
            <td class="gray right" width="150"><?=$field['PROPERTY_NAME']?>:</td>
            <td>
                <?=Common::buildFormField($field['PROPERTY_FORM'], $field['PARAM_NAME'], false, ['weight' => $field['PROPERTY_WEIGHT']])?>
            </td>
        </tr>
        <?}
    ?></table><?
}

if(!empty($fields[Model_Report::REPORT_CONSTRUCTOR_TYPE_ADDITIONAL])){
    ?><table>
        <tr>
            <td class="td_title" width="150">Дополнительные параметры:</td>
            <td>
                <table class="report_additional_params">
                    <?
                    foreach($fields[Model_Report::REPORT_CONSTRUCTOR_TYPE_ADDITIONAL] as $field){?>
                        <tr>
                            <td class="gray right vaTop" width="150"><?=$field['PROPERTY_NAME']?>:</td>
                            <td>
                                <?=Common::buildFormField($field['PROPERTY_FORM'], $field['PARAM_NAME'], false, ['weight' => $field['PROPERTY_WEIGHT']])?>
                            </td>
                        </tr>
                    <?}
                    ?></table>
            </td>
        </tr>
    </table><?
}

if(!empty($fields[Model_Report::REPORT_CONSTRUCTOR_TYPE_FORMAT])){
    ?><table>
        <tr>
            <td class="gray right" width="150">Формат:</td>
            <td>
                <?foreach($fields[Model_Report::REPORT_CONSTRUCTOR_TYPE_FORMAT] as $field){?>
                    <?=Common::buildFormField($field['PROPERTY_FORM'], $field['PARAM_NAME'])?>
                <?}?>
            </td>
        </tr>
    </table><?
}
?>
<table>
    <tr>
        <td width="150"></td>
        <td>
            <br>
            <span class="btn" onclick="generateReport($(this))"><i class="icon-download"></i> Сформировать</span>
            <!-- span class="btn btn_orange"><i class="icon-notifications"></i> На почту</span -->
        </td>
    </tr>
</table>