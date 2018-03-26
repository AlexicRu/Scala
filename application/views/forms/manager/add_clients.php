<div class="clients_list_autocomplete_block items_list_autocomplete_block">
    <div class="input_out">
        <div class="input_with_icon">
            <i class="icon-find"></i>
            <input type="text" class="input_big" placeholder="Начните вводить имя клиента" onkeyup="loadClientList($(this))">
        </div>
    </div>
    <div class="found_items_list"></div>
    <div class="selected_items_list"></div>

    <div class="right">
        <button class="btn btn_reverse btn_manager_add_clients_go" onclick="managerAddClients($(this))"><i class="icon-ok"></i> Добавить</button>
        <span class="btn btn_red fancy_close">Отмена</span>
    </div>
</div>