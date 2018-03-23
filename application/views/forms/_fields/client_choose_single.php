<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox" autocomplete="off"
        <?=(isset($params['weight']) ? 'weight="'.$params['weight'].'"' : '')?>
        url="/help/list-client">
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            var t = $(this);
            renderComboBox(t, <?=json_encode($params)?>);
            setFormFieldValue(t.parent(), '<?=$value?>');
        });
    });
</script>