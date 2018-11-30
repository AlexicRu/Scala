<form>
    <?if(!empty($detail['NOTE_ID'])){?>
        <input type="hidden" name="news_edit_id" value="<?=$detail['NOTE_ID']?>">
        <input type="hidden" name="news_edit_image_path" value="<?=$detail['PICTURE']?>">
    <?}?>
    <table class="table_form form_news_add">
        <tr>
            <td class="gray right" width="170">Заголовок:</td>
            <td>
                <input type="text" name="news_edit_title" class="input_big" value="<?=(empty($detail['NOTE_TITLE']) ? '' : $detail['NOTE_TITLE'])?>">
            </td>
        </tr>
        <tr>
            <td class="gray right">Дата:</td>
            <td>
                <input type="text" class="input_big datepicker" readonly name="news_edit_date" value="<?=(empty($detail['NOTE_DATE']) ? date('d.m.Y') : $detail['NOTE_DATE'])?>">
            </td>
        </tr>
        <?if($user['AGENT_ID'] == 0 && empty($detail)){?>
            <tr>
                <td class="gray right">Рассылки:</td>
                <td>
                    <div class="m-b-5">
                        <label><input type="radio" name="news_edit_subscribe" onclick="toggleSelectSubscribeAgent($(this))" value="all" checked> По всем агнетам</label><br>
                        <label><input type="radio" name="news_edit_subscribe" onclick="toggleSelectSubscribeAgent($(this))" value="group"> По группе агентов</label>
                    </div>
                    <select name="news_edit_subscribe_agent" class="select_big" disabled>
                        <?foreach(Listing::getAgents() as $agent){?>
                            <option value="<?=$agent['GROUP_ID']?>"><?=$agent['GROUP_NAME']?></option>
                        <?}?>
                    </select>
                </td>
            </tr>
        <?}?>
        <tr>
            <td class="gray right">Фото:</td>
            <td>
                <div class="news_edit_image dropzone"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea name="news_edit_text"></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="fr">
                <?if(!empty($detail['NOTE_ID'])){?>
                    <span class="btn btn_reverse btn_news_edit_go"><i class="icon-ok"></i> Редактировать</span>
                <?}else{?>
                    <span class="btn btn_reverse btn_news_edit_go">+ Добавить</span>
                <?}?>
                <span class="btn btn_red fancy_close">Отмена</span>
            </td>
        </tr>
    </table>
</form>

<script>
    var editor = $('[name=news_edit_text]');
    var image = false;

    $(function(){
        $('input[type=radio]').each(function () {
            renderRadio($(this));
        });

        dropzone = new Dropzone('.news_edit_image', {
            url: "/index/upload-file?component=image",
            autoProcessQueue: false,
            addRemoveLinks: true,
            maxFiles: 1,
            success: function(file, response)
            {
                if(response.success){
                    image = response.data.file.file;
                }
            },
            queuecomplete: function ()
            {
                goAddNews();
            }
        });

        initWYSIWYG(editor);

        <?if(!empty($detail['NOTE_ID'])){?>
            editor.trumbowyg('html', $('.news_elem .n_body').html());
        <?}?>

        $('.btn_news_edit_go').on('click', function(){

            if(dropzone.getAcceptedFiles().length){
                dropzone.processQueue();
            }else{
                goAddNews();
            }
        });
    });

    function toggleSelectSubscribeAgent(radio)
    {
        $('[name=news_edit_subscribe_agent]').prop('disabled', radio.val() == 'all');
    }

    function goAddNews()
    {
        var params = {
            id:                 $('[name=news_edit_id]').val(),
            title:              $('[name=news_edit_title]').val(),
            date:               $('[name=news_edit_date]').val(),
            body:               editor.trumbowyg('html'),
            image:              image ? image : $('[name=news_edit_image_path]').val(),
            subscribe:          $('[name=news_edit_subscribe]:checked').val(),
            subscribe_agent:    $('[name=news_edit_subscribe_agent]').val(),
            type:               <?=Model_Note::NOTE_TYPE_NEWS?>
        };

        if(params.title == ''){
            message(0, 'Введите заголовок новости');
            return false;
        }

        $.post('/news/note-edit', {params:params}, function(data){
            if(data.success){
                <?if(!empty($detail['NEWS_ID'])){?>
                    message(1, 'Новость успешно отредактированна');
                <?}else{?>
                    message(1, 'Новость успешно добавлена');
                <?}?>
                $.fancybox.close();
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }else{
                <?if(!empty($detail['NEWS_ID'])){?>
                    message(0, 'Ошибка редактирования новости');
                <?}else{?>
                    message(0, 'Ошибка добавления новости');
                <?}?>
            }
            dropzone.removeAllFiles();
            image = false;
        });
    }
</script>