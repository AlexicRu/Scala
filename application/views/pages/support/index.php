<h1>Поддержка</h1>

<div class="tabs_block tabs_switcher">
    <div class="tabs">
        <span tab="feedback" class="tab active"><i class="icon-notifications"></i> Обратная связь</span><?if (Access::file('Инструкция_по_работе_с_ЛК_системы_Администратор.docx')){?><span tab="documents" class="tab"><i class="icon-contract"></i> Документы</span><?}?>
    </div>
    <div class="tabs_content">
        <div tab_content="feedback" class="tab_content active">
            <?=$feedbackForm?>
        </div>
        <?if (Access::file('Инструкция_по_работе_с_ЛК_системы_Администратор.docx')){?>
        <div tab_content="documents" class="tab_content">
            <b>Инструкции:</b><br><br>
            <span class="f20">Инструкция по работе с ЛК системы</span> &nbsp; <a href="/file/Инструкция_по_работе_с_ЛК_системы_Администратор.docx" class="btn btn_small"><i class="icon-download icon"></i> Скачать</a>
        </div>
        <?}?>
    </div>
</div>