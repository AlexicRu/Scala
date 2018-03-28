<span class="form_field" field="<?=$type?>">
    <input type="text" name="<?=$name?>_start" class="input_big datepicker" readonly value="<?=date('01.m.Y')?>"> -
    <input type="text" name="<?=$name?>_end" class="input_big datepicker" readonly value="<?=date('d.m.Y')?>">
</span>

<script>
    $(function () {
        $('[name=<?=$name?>_start], [name=<?=$name?>_end]').each(function () {
            renderDatePicker($(this));
        });
    });
</script>