<!-- Integration Extras Promotions Section-->
<?php

use Src\Controller\IntegrationController;

$postedDiscount = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'discount')['10'];
//$promotions = IntegrationController::GetPromotion($integration->gfUid, $integration->Id);
$noDItemChecked = false;
if (isset($_POST["fetchDiscounts"])) {
    $dItem = fetchDiscounts();
    if ($dItem != null) {


        $l_id = $dItem[0]->id;
        $respItem = $integrationGateway->InsertOrUpdatePostedType("Online Rabat", "10", "discount", $integration->Id, $gfMenu->menu_id, $l_id, null, null);
        $postedDiscount = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'discount')[10];
    } else {
        echo "No Delivery Fee Item";
    }
    $noDItemChecked = true;
}

if (isset($_POST["postDiscounts"])) {
    IntegrationController::PostDiscount($integration, $gfMenu->menu_id);
    $postedDiscount = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'discount')[10];
    $noDItemChecked = true;
}

function fetchDiscounts()
{
    global $integration;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.loyverse.com/v1.0/discounts?show_deleted=false",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));

    $response = curl_exec($curl);

    $usable_discounts = array_filter(json_decode($response)->discounts, function ($obj) {
        return $obj->type == "VARIABLE_AMOUNT";
    });

    //array_splice( $usable_discounts, 0, 0, array((object)array("id"=>null,"name"=>"< Create New >"))); 
    curl_close($curl);
    return $usable_discounts;
}
?>
<!-- <div class="row">
    <div class="col d-grid gap-2 mx-auto">
        <span class="btn btn-success fs-4" name="postPromotions" id="btnPostPromotions">Post Promotions</span>
    </div>
</div>
<div class="row">
    <div class="table-responsive-sm">
         <table id="tblPromotions" class="table table-striped  w-100">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Outcomes</th>
                    <th>Outcomes function</th>
                    <th>Outcomes discount</th>
                    <th>Udated at</th>
                    <th>Loyverse Id</th>
                    <th>Select Discount</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="promotion">
            </tbody>
        </table> 
    </div>
</div> -->

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