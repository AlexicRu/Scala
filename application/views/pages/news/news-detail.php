<div class="back_link">&larr; <a href="/news">Назад к списку</a></div>

<h1>
    Новости

    <?if(Access::allow('news_news-edit', true) && ($detail['MANAGER_ID'] != 0 || ($detail['MANAGER_ID'] == 0 && $user['AGENT_ID'] == 0))){?>
        <a href="#news_edit" class="btn fancy">Редактировать новость</a>
    <?}?>
</h1>

<div class="news_elem block">
    <h2><?if($detail['MANAGER_ID'] == 0){?><i class="icon-user"></i> <?}?><?=$detail['NOTE_TITLE']?></h2>
    <div class="n_date gray"><?=$detail['NOTE_DATE']?></div>
    <?if(!empty($detail['PICTURE'])){?>
        <div class="n_img_detail"><img src="<?=$detail['PICTURE']?>"></div>
    <?}?>
    <div class="n_body"><?=$detail['NOTE_BODY']?></div>
</div>

<?if(Access::allow('news_news-edit', true)){?>
    <?=$popupNewsEdit?>
<?}?>