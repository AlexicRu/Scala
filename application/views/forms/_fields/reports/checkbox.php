<input type="checkbox" class="form_field" name="<?=$name?>" field="<?=$type?>"
    <?=(isset($params['weight']) ? 'weight="'.$params['weight'].'"' : '')?>
>
<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderCheckbox($(this));
        });
    });
</script>