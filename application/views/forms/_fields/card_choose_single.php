<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox input_wide <?=(!empty($params['classes']) ? $params['classes'] : '')?>" url="/help/list-card" autocomplete="off"
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