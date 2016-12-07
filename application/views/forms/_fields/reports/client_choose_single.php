<?
$clients = Model_Client::getClientsList();
?>
<select class="select_big form_field" name="<?=$name?>" field="<?=$type?>">
    <option value="0"> -- Выберите клиента -- </option>
    <?foreach ($clients as $client){?>
        <option value="<?=$client['CLIENT_ID']?>"><?=$client['CLIENT_NAME']?></option>
    <?}?>
</select>