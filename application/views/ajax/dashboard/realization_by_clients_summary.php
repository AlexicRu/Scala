<table>
    <tr>
        <td class="gray right">Количество литров:</td>
        <td class="right"><?=number_format($data['SERVICE_AMOUNT'], 2, '.', '&nbsp;')?> л.</td>
    </tr>
    <tr>
        <td class="gray right">Выручка:</td>
        <td class="right"><?=number_format($data['SUMPRICE_DISCOUNT'], 2, '.', '&nbsp;')?> <?=Text::RUR?></td>
    </tr>
    <?if (Access::allow('view_full_dashboard_clients_summary')) {?>
    <tr>
        <td class="gray right">Стоимость на АЗС:</td>
        <td class="right"><?=number_format($data['SERVICE_SUMPRICE'], 2, '.', '&nbsp;')?> <?=Text::RUR?></td>
    </tr>
    <tr>
        <td class="gray right">Закупки:</td>
        <td class="right"><?=number_format($data['SUMPRICE_BUY'], 2, '.', '&nbsp;')?> <?=Text::RUR?></td>
    </tr>
    <tr>
        <td class="gray right">Маржинальный доход:</td>
        <td class="right"><?=number_format($data['MARGINALITY'], 2, '.', '&nbsp;')?> <?=Text::RUR?></td>
    </tr>
    <tr>
        <td class="gray right">Средняя скидка клиенту:</td>
        <td class="right"><?=number_format($data['AVG_DISCOUNT'], 5, '.', '&nbsp;')?> <?=Text::RUR?></td>
    </tr>
    <?}?>
</table>