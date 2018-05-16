<div class="as_table">
    <div class="col">
        <table class="table_form">
            <tr>
                <td class="gray right" width="170">Email для ответов:</td>
                <td>
                    <input type="text" name="feedback_email" class="input_grand">
                </td>
            </tr>
            <tr>
                <td class="gray right">Тема сообщения:</td>
                <td>
                    <input type="text" name="feedback_subject" class="input_grand">
                </td>
            </tr>
            <tr>
                <td class="gray right">Текст сообщения:</td>
                <td>
                    <textarea name="feedback_text" style="width: 500px"></textarea>
                </td>
            </tr>
            <tr>
                <td class="gray right">Прикрепленные файлы:</td>
                <td>
                    <div class="feedback_files dropzone"></div>
                    <i class="gray">Максимальный размер файлов - 3 MB, максимум файлов - 5 шт</i>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button class="btn btn_green btn_reverse btn_feedback" onclick="preFeedback($(this))"><i class="icon-ok"></i> Сохранить</button>
                </td>
            </tr>
        </table>
    </div>
</div>


<script>
    var files = [];

    $(function () {
        dropzone = new Dropzone('.feedback_files', {
            url: "/index/upload-file",
            autoProcessQueue: false,
            addRemoveLinks: true,
            maxFilesize: 3,
            maxFile: 5,
            success: function(file, response)
            {
                if(response.success){
                    files.push(response.data.file);
                    dropzone.options.autoProcessQueue = true;
                }
            },
            queuecomplete: function ()
            {
                dropzone.options.autoProcessQueue = false;
                doFeedback();
            }
        });
    });

    function preFeedback(btn) {
        if (checkBtnLoading(btn)) {
            return false;
        }

        if(dropzone.getQueuedFiles().length){
            dropzone.processQueue();
        }else{
            doFeedback();
        }
    }

    function doFeedback()
    {
        var btn = $('.btn_feedback');
        var params = {
            email:      $('[name=feedback_email]').val(),
            subject:    $('[name=feedback_subject]').val(),
            text:       $('[name=feedback_text]').val(),
            files:      files
        };

        if(params.email == '' || params.email.indexOf('@') == -1){
            message(0, 'Введите корректный email');
            return false;
        }

        if(params.subject == ''){
            message(0, 'Введите тему сообщения');
            return false;
        }

        toggleBtnLoading(btn);

        $.post('/support/feedback', params, function(data){
            if(data.success){
                message(1, 'Сообщение успешно добавлено');
            }else{
                message(0, data.data ? data.data : 'Ошибка отправки сообщения');
            }

            dropzone.removeAllFiles();
            files = [];
            $('[name=feedback_email]').val('');
            $('[name=feedback_subject]').val('');
            $('[name=feedback_text]').val('');

            toggleBtnLoading(btn);
        });
    }

</script>