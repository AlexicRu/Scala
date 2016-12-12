<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="combobox combobox_multi" url="/help/list_country" autocomplete="off">
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderComboBoxMulti($(this));
        });
    });
</script>