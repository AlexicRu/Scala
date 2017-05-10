<h1>Поставщики</h1>

<div class="ajax_block_suppliers_out block_loading">

</div>

<script>
    $(function(){
        paginationAjax('/suppliers/suppliers_list/', 'ajax_block_suppliers', renderAjaxPaginationSuppliers, {show_all_btn: true});
    });

    function renderAjaxPaginationSuppliers(data, block)
    {
        for(var i in data){
            var tpl = $('<div class="block supplier">' +
                '<div class="s_logo" />' +
                '<a class="s_name" />' +
                '<div class="s_info" / >' +
            '</div>');

            tpl.data('supplier_id', data[i].ID);
            tpl.find('.s_name').text(data[i].SUPPLIER_NAME).attr('href', '/suppliers/' + data[i].ID);
            tpl.find('.s_info').text(data[i].LONG_NAME);

            if (data[i].SUPPLIER_LOGO) {
                tpl.find('.s_logo').css('background-image', 'url("'+ data[i].SUPPLIER_LOGO +'")');
            } else {
                tpl.find('.s_logo').addClass('s_logo_empty');
            }

            block.append(tpl);
        }
    }
</script>