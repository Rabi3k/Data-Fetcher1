<?php
$payemntRelations =  $paymentRelationGateway->findByKey($integration->Id,"default");

?>
<section id="payment-cash">
    <div class="mb-3 row">
        <div class="col-4">
            <label for="inputName" class="col-form-label fs-5 fw-bold">Default Payment </label>
        </div>
        <div class="col-5">
            <select name="payments" id="slPayments" class="form-select form-select-lg mb-1" aria-label=".form-select-lg slPayments">
            </select>
        </div>
        <div class="col-3">
            <div class="d-flex justify-content-center align-items-center">
                <div class="col">
                    <button type="button" class="btn btn-info text-light fs-6 " id="btnSaveDefaultPayment">
                        Save
                    </button>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-info text-light fs-6 " onclick="loadLPaymentRelations();">
                        Refresh
                    </button>
                </div>
                <div class="col-2">
                    <span class="spinner-border text-secondary spinner-border-sm float-end visually-hidden" id="pr-spinner" role="status">
                    </span>
                </div>
    
            </div>
    
        </div>
    </div>
</section>

<div class="row">
    <div class="col d-grid gap-2 mx-auto">
        <span class="btn btn-success fs-4" name="postPromotions" id="btnSavePayments">Save Payments</span>
    </div>
</div>
<div class="row my-3">
    <div class="table-responsive-sm">
         <table id="tblpaymentRelations" class="table table-striped  w-100">
            <thead class="table-secondary">
                <tr>
                    <th>GF Payment</th>
                    <th>Loyverse Payment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="paymentRelations">
            </tbody>
        </table> 
    </div>
</div> 

<script type="text/javascript">
    <?php include "js/paymentRelations.min.js" ?>
</script>