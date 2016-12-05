<input type="text" name="<?=$name?>_start" class="input_big datepicker" readonly value="<?=date('01.m.Y')?>"> -
<input type="text" name="<?=$name?>_end" class="input_big datepicker" readonly value="<?=date('d.m.Y')?>">

<script>
    $(function () {
        renderDatePicker($('[name=<?=$name?>_start]'));
        renderDatePicker($('[name=<?=$name?>_end]'));
    });
</script>