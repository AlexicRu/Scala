<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="combobox combobox_multi" url="/help/list_manager">
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderComboBox($(this));
        });
    });
</script>