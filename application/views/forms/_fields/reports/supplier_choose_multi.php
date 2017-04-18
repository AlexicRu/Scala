<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="combobox combobox_multi input_wide" url="/help/list_supplier" autocomplete="off"
        <?=(isset($params['weight']) ? 'weight="'.$params['weight'].'"' : '')?>
    >
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderComboBoxMulti($(this));
        });
    });
</script>
