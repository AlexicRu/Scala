<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>" class="custom_field datepicker" readonly>
</span>
<script>
    $(function () {
        $('[name=<?=$name?>]').each(function () {
            var t = $(this);

            renderDatePicker(t);

            <?if (!empty($value)) {?>
                t.datepicker("setDate", '<?=$value?>');
            <?}?>
        });
    });
</script>