<div class="back_link">&larr; <a href="/news">Назад к списку</a></div>

<h1>Новости</h1>

<div class="news_elem block">
    <h2><?=$detail['TITLE']?></h2>
    <div class="n_date gray"><?=$detail['DATE_CREATE_WEB']?></div>
    <?if(!empty($detail['PICTURE'])){?>
        <div class="n_img_detail"><img src="<?=$detail['PICTURE']?>"></div>
    <?}?>
    <div class="n_body"><?=$detail['CONTENT']?></div>
</div>