<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox combobox_multi input_wide" url="/help/list_service" autocomplete="off">
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            var t = $(this);

            renderComboBoxMulti(t, '<?=json_encode($params)?>');
            setFormFieldValue(t.parent(), '<?=$value?>');
        });
    });
</script>