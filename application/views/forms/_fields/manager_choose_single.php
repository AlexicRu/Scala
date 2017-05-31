<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox input_wide" url="/help/list_manager" autocomplete="off"
        <?=(isset($params['weight']) ? 'weight="'.$params['weight'].'"' : '')?>
    >
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderComboBox($(this));
        });
    });
</script>