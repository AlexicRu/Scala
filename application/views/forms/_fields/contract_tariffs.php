<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="combobox input_wide <?=(!empty($params['classes']) ? $params['classes'] : '')?>" url="/help/list_contract_tariffs" autocomplete="off">
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            var t = $(this);

            renderComboBox(t);
            setFormFieldValue(t.parent(), '<?=$value?>');
        });
    });
</script>