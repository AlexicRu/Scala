<h1>Тарифы</h1>

<div class="tabs_vertical_block tabs_switcher tabs_tariffs">
    <div class="tabs_v">
        <div class="tab_v tab_v_small active" tab="1"><div>
                <a href="#">Тариф 1</a>
            </div></div>
        <div class="tab_v tab_v_small" tab="4"><div>
                <a href="#">Тариф 2</a>
            </div></div>
        <div class="tab_v tab_v_small" tab="2"><div>
                <a href="#">Тариы 3</a>
            </div></div>
    </div>
    <div class="tabs_v_content">
        <div class="tab_v_content active tariffs_block" tab_content="1">
            <table>
                <tr>
                    <td class="gray right" width="200">Название:</td>
                    <td>
                        <input type="text" class="input_big">
                    </td>
                </tr>
            </table>

            <div class="t_sections_list">
                <fieldset>
                    <legend>Секция 1</legend>
                    <span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span>

                    <div class="ts_conditions">
                        <div class="tsc_item line_inner">
                            <span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span>

                            <div class="line_inner_100">Условие:</div>
                            <select>
                                <option>Условие 1</option>
                            </select>
                            <select>
                                <option>=</option>
                                <option>содержит</option>
                            </select>
                            <input type="text">
                        </div>
                        <div class="tsc_item line_inner">
                            <span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span>

                            <div class="line_inner_100">Условие:</div>
                            <select>
                                <option>Условие 2</option>
                            </select>
                            <select>
                                <option>=</option>
                                <option>содержит</option>
                            </select>
                            <input type="text">
                        </div>
                    </div>

                    <span class="btn btn_add_condition btn_small">+ Добавить условие</span>

                    <br><br>

                    <b class="f18">Параметры:</b>
                    <table>
                        <tr>
                            <td class="gray right" width="100">Тип:</td>
                            <td>
                                <select>
                                    <option>тип 1</option>
                                </select>
                            </td>
                            <td class="gray right" width="100">Параметр:</td>
                            <td>
                                <select>
                                    <option>параметр 1</option>
                                </select>
                            </td>
                            <td class="gray right" width="100">Размер:</td>
                            <td>
                                <input type="text">
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
            <span class="btn btn_add_section">+ Добавить секцию</span>

            <div class="row_btns">
                <span class="btn btn_green"><i class="icon-ok"></i> Сохранить</span>
            </div>
        </div>
        <div class="tab_v_content" tab_content="2">2</div>
        <div class="tab_v_content" tab_content="3">3</div>
    </div>
</div>

<script>
    $(function(){
        $('.btn_add_section').on('click', function () {
            var t = $(this);
            var block = t.closest('.tariffs_block');
            var list = block.find('.t_sections_list');

            var tpl = $('<fieldset><legend>Секция</legend><span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span></fieldset>');

            tpl.appendTo(list);
        });
        $('.btn_add_condition').on('click', function () {
            var t = $(this);
            var block = t.closest('.tariffs_block');
            var list = block.find('.ts_conditions');

            var tpl = $('<div class="tsc_item line_inner"><span class="btn btn_small btn_icon btn_red ts_remove"><i class="icon-cancel"></i></span></div>');

            tpl.appendTo(list);
        });

        $(document).on('click', '.ts_remove', function () {
            var t = $(this);
            var fieldset = t.closest('fieldset, .tsc_item');

            fieldset.remove();
        });
    });
</script>