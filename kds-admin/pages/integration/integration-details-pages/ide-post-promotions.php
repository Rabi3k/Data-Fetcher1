<!-- Integration Extras Promotions Section-->
<?php
$postedDiscount = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'discount')['10'];
?>

<div class="mb-3 row">
    <div class="col-4">
        <label for="inputName" class="col-form-label fs-5 fw-bold">Selected default Discount </label>
    </div>
    <div class="col-5">
        <select name="discounts" id="slDiscounts" class="form-select form-select-lg mb-1" aria-label=".form-select-lg slDiscounts">
            <optgroup label="New Discount">
                <option id="osc-new" value="0">Create New</option>
            </optgroup>
            <optgroup label="-= Variable Amount Discounts =-" class="variable-amount">
            </optgroup>
        </select>
    </div>
    <div class="col-3">
        <div class="d-flex justify-content-center align-items-center">
            <div class="col">
                <button type="button" class="btn btn-info text-light fs-6 " onclick="saveLDisounts();">
                    Save
                </button>
            </div>
            <div class="col">
                <button type="button" class="btn btn-info text-light fs-6 " onclick="loadLDisounts();">
                    Refresh
                </button>
            </div>
            <div class="col-2">
                <div class="spinner-border text-secondary spinner-border-sm float-end visually-hidden" id="dp-spinner" role="status">
                </div>
            </div>

        </div>

    </div>
</div>
<script type="text/javascript">
    let discountLoyveresId = `<?php echo isset($postedDiscount) && isset($postedDiscount->loyverse_id) ? $postedDiscount->loyverse_id : "" ?>`;
    <?php include "js/promotions.min.js" ?>
</script>
<!--END Integration Extras Promotions Section-->