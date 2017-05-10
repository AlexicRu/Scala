<script src="/js/suppliers/supplier_detail.js"></script>

<div class="back_link">&larr; <a href="/suppliers">Вернуться назад</a></div>

<input type="hidden" name="supplier_id" value="<?=$supplier['ID']?>">

<div class="supplier-detail__info">
    <?include('supplier_detail/info.php')?>
</div>

<div class="line"></div>

<pre>
<?print_r($supplier)?>
</pre>
