<input type="checkbox" class="form_field" name="<?=$name?>" field="<?=$type?>">
<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderCheckbox($(this));
        });
    });
</script>