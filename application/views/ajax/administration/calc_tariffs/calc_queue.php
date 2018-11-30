<?if (empty($queue)) {?>
    <i class="gray">Нет текущих расчетов</i>
<?} else {?>
    <table class="table table_small">
        <tr>
            <th>Запись</th>
            <th>Клиент</th>
            <th>Договор</th>
            <th>Тариф</th>
            <th>Начало периода</th>
            <th>Окончание периода</th>
            <th>Статус</th>
        </tr>
        <?foreach ($queue as $row) {?>
            <tr>
                <td><?=$row['RECORD_ID']?></td>
                <td><?=$row['CLIENT_NAME']?></td>
                <td><?=$row['CONTRACT_NAME']?></td>
                <td><?=$row['TARIF_NAME']?></td>
                <td><?=$row['DATE_BEGIN_STR']?></td>
                <td><?=$row['DATE_END_STR']?></td>
                <td><?=$row['RECORD_STATUS']?></td>
            </tr>
        <?}?>
    </table>

    <br>
    <i class="gray">Записи в очереди хранятся в течение одного дня</i>
<?}?>