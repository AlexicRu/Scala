<?print_r($cards)?>
<div class="tc_top_line">
    <span class="gray">Всего карт:</span> 4 &nbsp;&nbsp;&nbsp;
    <span class="gray">В работе:</span> 1 &nbsp;&nbsp;&nbsp;
    <span class="red">Заблокировано: 1</span>
    <div class="fr input_with_icon"><i class="icon-find"></i><input type="text" class="input_big" placeholder="Поиск..."></div>
</div>
<div class="tabs_vertical_block tabs_switcher">
    <div class="tabs_v">
        <div class="tab_v" tab="1"><div>
                <a href="#"><span class="icon-card"></span> Добавить карту</a>
            </div></div>
        <div class="tab_v active" tab="2"><div>
                <span class="icon-card gray"></span>
                123456789000554856
                <div class="gray">Ivanov Sergey</div>
            </div></div>
        <div class="tab_v" tab="3"><div>
                <span class="icon-card gray"></span>
                123456789000554856
                <div class="gray">Ivanov Sergey</div>
                <span class="label label_error label_small">Заблокирована</span>
            </div></div>
        <div class="tab_v" tab="4"><div>
                <span class="icon-card gray"></span>
                123456789000554856
                <div class="gray">Ivanov Sergey</div>
            </div></div>
        <div class="tab_v gray preload"><div>
                <span class="icon-loader"></span> Загрузка карточек
            </div></div>
    </div>
    <div class="tabs_v_content">
        <div class="tab_v_content" tab_content="1">1</div>
        <div class="tab_v_content active" tab_content="2">
            <div class="fr">
                <button class="btn btn_red">Заблокировать</button> &nbsp;
                <button class="btn"><i class="icon-pen"></i> Редактировать</button>
            </div>

            <b class="f18">Обороты за текущий период:</b><br>
            152 л. / 5 122 ₽<br><br>

            <b class="f18">Последняя заправка:</b>
            <div class="line_inner">
                <span class="gray">06.01.2015</span> &nbsp;&nbsp;&nbsp; <b>АЗС Роснефть №15</b> <div class="fr">Бензин АИ-95 10л. / 372 ₽</div>
            </div>
            <br>
            <b class="f18">Ограничения по топливу:</b>
            <table>
                <tbody><tr>
                    <td class="gray right">
                        ДТ:<br>
                        ДТ зимнее:<br>
                        ДТ Евро:
                    </td>
                    <td class="line_inner">1000 л./сутки</td>
                </tr>
                <tr>
                    <td class="gray right">Бензин АИ-95:</td>
                    <td class="line_inner">150 ₽ единовременно</td>
                </tr>
                </tbody></table>
            <br>
            <b class="f18">История операций:</b>
            <div class="line_inner">
                <span class="gray">06.01.2015</span> &nbsp;&nbsp;&nbsp; Иванов И.И. <div class="fr">Статус - заблокирована</div>
            </div>
            <div class="line_inner">
                <span class="gray">06.01.2015</span> &nbsp;&nbsp;&nbsp; Петров А.Г. <div class="fr">Изменен лимит по карте</div>
            </div>
        </div>
        <div class="tab_v_content" tab_content="3">3</div>
        <div class="tab_v_content" tab_content="4">4</div>
    </div>
</div>
