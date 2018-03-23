<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox" url="/help/list-service" autocomplete="off">
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