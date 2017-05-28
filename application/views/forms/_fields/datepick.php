<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field datepicker" readonly>
</span>
<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            renderDatePicker($(this));
        });
    });
</script>