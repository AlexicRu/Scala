<?$dependFieldName = 'client_contract_choose_single';?>

<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox input_wide" url="/help/list_client" autocomplete="off" depend="<?=$dependFieldName?>">
</span>

<div>
    <?
    $data = [
        'placeholder' => 'Договор',
        'depend_on' => $name,
    ];
    if(isset($params['weight'])){
        $data['weight'] = $params['weight'];
    }
    ?>
    <?=Common::buildFormField('_depend/' . $dependFieldName, $dependFieldName, false, $data)?>
</div>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderComboBox($(this), {'depend': '<?=$dependFieldName?>'});
        });
    });
</script>