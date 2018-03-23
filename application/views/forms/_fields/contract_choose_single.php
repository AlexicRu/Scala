<?
$dependToField = 'client_contract_choose_single';
$dependToFieldName = !empty($params['depend_to_field_name']) ? $params['depend_to_field_name'] : $dependToField;
$params['placeholder'] = 'Договор';
$params['depend_on'] = [
    'field' => $dependToFieldName,
    'name' => 'client_id'
];
?>

<div class="with_depend">

    <div class="depend_to">
        <?
        $data = [
            'placeholder' => 'Клиент',
            'depend_to' => $name
        ];
        ?>
        <?=Common::buildFormField($dependToField, $dependToFieldName, false, $data)?>
    </div>

    <div class="depend_on">
        <span class="form_field" field="<?=$type?>">
            <input type="text" name="<?=$name?>" class="custom_field combobox <?=(!empty($params['classes']) ? $params['classes'] : '')?>" autocomplete="off"
                <?=(isset($params['weight']) ? 'weight="'.$params['weight'].'"' : '')?>
                <?if (!empty($params['depend_to'])){?>depend_to="<?=$params['depend_to']?>"<?}?>
                url="/help/list-clients-contracts">
        </span>
    </div>

</div>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            var t = $(this);

            renderComboBox(t, <?=json_encode($params)?>);
            <?if (!empty($value)) {?>
                setFormFieldValue(t.parent(), '<?=$value?>');
            <?}?>
        });
    });
</script>