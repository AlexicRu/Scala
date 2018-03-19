<?/*Common::addJs('suppliers/supplier_detail.js');*/?>
<script src="/assets/build/js/suppliers/supplier_detail.js"></script>

<div class="back_link">&larr; <a href="/suppliers">Вернуться назад</a></div>

<input type="hidden" name="supplier_id" value="<?=$supplier['ID']?>">

<div class="supplier-detail__info">
    <?include('supplier_detail/info.php')?>
</div>

<div class="line"></div>

<select name="suppliers_contracts_list" class="select_big select_long" onchange="contractId = $(this).val(); loadSupplierContract()">
    <?if(empty($supplierContracts)){?>
        <option value="0">Нет договоров</option>
    <?}else{
        foreach($supplierContracts as $contract){?>
            <option value="<?=$contract['CONTRACT_ID']?>">
                Договор: [<?=$contract['CONTRACT_ID']?>] <?=$contract['CONTRACT_NAME']?> от <?=$contract['DATE_BEGIN']?> <?if($contract['DATE_END'] != '31.12.2099'){?>до <?=$contract['DATE_END']?><?}?>
            </option>
        <?}}?>
</select>

<?if(Access::allow('suppliers_contract-add')){?>
    &nbsp;&nbsp;&nbsp;<a href="#supplier_contract_add" class="btn fancy">+ Создать договор</a>

    <?=$popupSupplierContractAdd?>
<?}?>

<div class="supplier-contract"></div>