<?
$dependToField = 'contract_choose_single';
$dependToFieldName = !empty($params['depend_to_field_name']) ? $params['depend_to_field_name'] : $dependToField;
$params['placeholder'] = 'Карта';
$params['depend_on'] = [
    'field' => $dependToFieldName,
    'name' => 'contract_id'
];
?>

<div class="with_depend">

    <div class="depend_to">
        <?
        $data = [
            'placeholder' => 'Договор',
            'depend_to' => $name
        ];
        ?>
        <?=Common::buildFormField($dependToField, $dependToFieldName, false, $data)?>
    </div>

    <div class="depend_on">
        <span class="form_field" field="<?=$type?>">
            <input type="text" name="<?=$name?>" class="custom_field combobox combobox_multi input_wide <?=(!empty($params['classes']) ? $params['classes'] : '')?>" autocomplete="off"
                <?=(isset($params['weight']) ? 'weight="'.$params['weight'].'"' : '')?>
                   url="/help/list-card">
        </span>
    </div>

</div>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            var t = $(this);

            renderComboBoxMulti(t, <?=json_encode($params)?>);
            <?if (!empty($value)) {?>
                setFormFieldValue(t.parent(), '<?=$value?>');
            <?}?>
        });
    });
</script>