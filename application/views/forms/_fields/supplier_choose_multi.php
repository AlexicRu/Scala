<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox combobox_multi input_wide" url="/help/list-supplier" autocomplete="off"
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
