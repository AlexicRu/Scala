<?if (empty($queue)) {?>
    <i class="gray">Нет текущих расчетов</i>
<?} else {?>
    <table class="table table_small">
        <tr>
            <th>RECORD_ID</th>
            <th>CLIENT_NAME</th>
            <th>CONTRACT_ID</th>
            <th>CONTRACT_NAME</th>
            <th>TARIF_NAME</th>
            <th>TARIF_VERSION</th>
            <th>DATE_BEGIN</th>
            <th>DATE_END</th>
            <th>RECORD_STATUS</th>
        </tr>
        <?foreach ($queue as $row) {?>
            <tr>
                <td><?=$row['RECORD_ID']?></td>
                <td><?=$row['CLIENT_NAME']?></td>
                <td><?=$row['CONTRACT_ID']?></td>
                <td><?=$row['CONTRACT_NAME']?></td>
                <td><?=$row['TARIF_NAME']?></td>
                <td><?=$row['TARIF_VERSION']?></td>
                <td><?=$row['DATE_BEGIN_STR']?></td>
                <td><?=$row['DATE_END_STR']?></td>
                <td><?=$row['RECORD_STATUS']?></td>
            </tr>
        <?}?>
    </table>
<?}?>