<h1>Услуги</h1>

<div class="block no_padding">

    <div class="as_table">
        <div class="col">
            <b class="f18">Выбрать источник:</b><br>
            <select class="sources_list">
                <?foreach ($tubesList as $tube) {?>
                    <option value="<?=$tube['TUBE_ID']?>"><?=$tube['TUBE_NAME']?></option>
                <?}?>
            </select>
            <br><br>
            <div class="services_list jsGrid"></div>
        </div>
        <div class="col line_inner">
            <b class="f18">Добавление услуги</b>

            <table>
                <tr>
                    <td class="gray right">В справочнике</td>
                    <td>
                        <?=Common::buildFormField('service_choose_single', 'add_service_in_service')?>
                    </td>
                </tr>
                <tr>
                    <td class="gray right">В источнике</td>
                    <td>
                        <input type="text" name="add_service_in_source" class="input_wide">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <span class="btn" onclick="addService()">Добавить</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>

<script>
    $(function () {
        changeSource($('.sources_list option:first').attr('value'));

        $('.sources_list').on('change', function () {
            changeSource($(this).val());
        });
    });

    var isAjax = false;
    function addService()
    {
        if (isAjax) {
            return false;
        }

        var params = {
            'tube_id':    $('.sources_list').val(),
            'service_id': getComboboxValue($('[name=add_service_in_service].combobox')),
            'name':       $('[name=add_service_in_source]').val(),
        };

        if (params.service_id == '' || params.name == '' || params.tube_id == '') {
            message('error', 'Заполните все поля');
            return false;
        }

        $.post('/references/add_convert_service', params, function (data) {
            if (data.success) {
                message('success', 'Услуга успшно добавлена');

                changeSource($('.sources_list option:selected').attr('value'));
            } else {
                message('error', 'Ошибка добавления услуги');
            }
        });
    }

    function changeSource(source)
    {
        var block = $('services_list');

        block.addClass(CLASS_LOADING);

        if (!source) {
            block.removeClass(CLASS_LOADING);
            message('error', 'Список услуг пуст');
            return false;
        }

        $.post('/references/service_list_load', {tube_id: source}, function (data) {
            if (data.success) {
                drawTable(data.data);
                //changeSelect(data.data);
            } else {
                message('error', 'Ошибка загрузки списка услуг');
            }

            block.removeClass(CLASS_LOADING);
        });
    }

    function changeSelect(data)
    {
        var select = $('[name=add_service_in_service]');

        select.empty();

        for (var i in data){
            var optionValue = data[i].DESCRIPTION;

            if (select.find('option[value="'+ optionValue +'"]').length == 0) {
                select.append('<option value="'+ optionValue +'">'+ data[i].DESCRIPTION +'</option>');
            }
        }
    }

    function drawTable(rows)
    {
        var grid = $(".jsGrid.services_list");

        grid.jsGrid({
            width: '100%',
            sorting: true,
            paging: true,
            pageSize: 10,

            data: rows,

            fields: [
                { name: "ID", type: "text", title: 'ID', width:100},
                { name: "DESCRIPTION", type: "text", title: 'Краткое наименование', width:100},
                { name: "SERVICE_IN_TUBE", type: "text", title: 'Полное наименование', width:150},
            ]
        });
    }
</script>