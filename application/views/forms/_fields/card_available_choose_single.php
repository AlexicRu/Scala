<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field combobox input_wide <?=(!empty($params['classes']) ? $params['classes'] : '')?>" url="/help/list_cards_available" autocomplete="off">
</span>

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderComboBox($(this));
        });
    });
</script>