<form>
<table class="table_form form_news_add">
    <tr>
        <td class="gray right" width="170">Заголовок:</td>
        <td>
            <input type="text" name="news_add_title" class="input_big">
        </td>
    </tr>
    <tr>
        <td class="gray right">Дата:</td>
        <td>
            <input type="text" class="input_big datepicker" readonly name="news_add_date" value="<?=date('d.m.Y')?>">
        </td>
    </tr>
    <tr>
        <td class="gray right">Фото:</td>
        <td>
            <div class="news_add_image dropzone"></div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea name="news_add_text"></textarea>
        </td>
    </tr>
    <tr>
        <td></td>
        <td class="fr">
            <span class="btn btn_reverse btn_news_add_go">+ Добавить новость</span>
            <span class="btn btn_red fancy_close">Отмена</span>
        </td>
    </tr>
</table>
    </form>

<script>
    var editor = $('[name=news_add_text]');
    var image = false;

    $(function(){
        dropzone = new Dropzone('.news_add_image', {
            url: "/index/upload_image",
            autoProcessQueue: false,
            addRemoveLinks: true,
            maxFiles: 1,
            success: function(file, response)
            {
                if(response.success){
                    image = response.data.file;
                }
            },
            queuecomplete: function ()
            {
                goAddNews();
            }
        });

        initWYSIWYG(editor);

        $('.btn_news_add_go').on('click', function(){

            if(dropzone.getAcceptedFiles().length){
                dropzone.processQueue();
            }else{
                goAddNews();
            }
        });
    });

    function goAddNews()
    {
        var params = {
            title:  $('[name=news_add_title]').val(),
            date:   $('[name=news_add_date]').val(),
            text:   editor.trumbowyg('html'),
            image:  image
        };

        if(params.title == ''){
            message(0, 'Введите заголовок новости');
            return false;
        }

        $.post('/news/news_add', {params:params}, function(data){
            if(data.success){
                message(1, 'Новость успешно добавлена');
                $.fancybox.close();
            }else{
                message(0, 'Ошибка добавления новости');
            }
            dropzone.removeAllFiles();
            image = false;
        });
    }
</script>