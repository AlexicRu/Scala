<span class="form_field" field="<?=$type?>">
    <input type="checkbox" class="custom_field" name="<?=$name?>"
        <?=(isset($params['weight']) ? 'weight="'.$params['weight'].'"' : '')?>
    >
</span>
<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderCheckbox($(this));
        });
    });
</script>