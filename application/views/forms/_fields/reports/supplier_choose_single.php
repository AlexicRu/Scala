<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="combobox input_wide" url="/help/list_supplier" autocomplete="off">
</span>
<?$dependFieldName = $name.'_contract_id';?>
<div>
    <?=Common::buildFormField('_depend', 'supplier_contract_choose_single', $dependFieldName, false, [
        'placeholder' => 'Договор',
        'depend_on' => $name
    ])?>
</div>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderComboBox($(this), {'depend': '<?=$dependFieldName?>'});
        });
    });
</script>