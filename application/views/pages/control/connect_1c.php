<h1>Связь с 1с</h1>

<div class="tabs_block tabs_switcher tabs_connect_1c">
    <div class="tabs">
        <span tab="payments" class="tab active">Загрузка платежей</span><span tab="documents" class="tab">Загрузка отчетных документов</span>
    </div>
    <div class="tabs_content">
        <div tab_content="payments" class="tab_content active">
            <div class="connect_1c_payments dropzone"></div>
        </div>
        <div tab_content="documents" class="tab_content">
        </div>
    </div>
</div>

<script>
    $(function(){
        dropzone = new Dropzone('.connect_1c_payments', {
            url: "/index/upload_file",
            //autoProcessQueue: false,
            addRemoveLinks: true,
            //maxFiles: 1,
            success: function(file, response)
            {
                if(response.success){
                    image = response.data.file;
                }
            },
            queuecomplete: function ()
            {
                //goAddNews();
            }
        });
    });
</script>