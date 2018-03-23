<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox <?=(!empty($params['classes']) ? $params['classes'] : '')?>" url="/help/list-contract-tariffs" autocomplete="off">
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