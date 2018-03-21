<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox input_wide" url="/help/list-client" autocomplete="off"
        <?if (!empty($params['depend_to'])){?>depend_to="<?=$params['depend_to']?>"<?}?>
        <?if (isset($params['weight'])){?>weight="<?=$params['weight']?>"<?}?>
    >
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            var t = $(this);

            renderComboBox(t, <?=json_encode($params)?>);
            <?if (!empty($value)) {?>
                setFormFieldValue(t.parent(), '<?=$value?>');
            <?}?>
        });
    });
</script>