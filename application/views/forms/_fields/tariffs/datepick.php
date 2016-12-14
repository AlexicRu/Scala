<input type="text" name="<?=$name?>" class="form_field datepicker" readonly field="<?=$type?>">

<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderDatePicker($(this));
        });
    });
</script>