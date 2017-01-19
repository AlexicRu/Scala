<input type="text" class="time_mask_input form_field input_mini" name="<?=$name?>" field="<?=$type?>" placeholder="__:__:__">

<script>
    $(function () {
        $('[name=<?=$name?>]').mask('00:00:00');
    });
</script>