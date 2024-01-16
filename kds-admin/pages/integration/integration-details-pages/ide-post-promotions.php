<!-- Integration Extras Promotions Section-->
<?php

use Src\Controller\IntegrationController;
$postedDiscount = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'discount');
//$promotions = IntegrationController::GetPromotion($integration->gfUid, $integration->Id);
$noDItemChecked = false;
if (isset($_POST["fetchDiscounts"])) {
    $dItem = fetchDiscounts();
    if ($dItem != null) {
        
       
        $l_id = $dItem[0]->id;
        $respItem = $integrationGateway->InsertOrUpdatePostedType("Online Rabat", "10", "discount", $integration->Id,$gfMenu->menu_id, $l_id, null, null);
        $postedDiscount = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'discount');
    } else {
        echo "No Delivery Fee Item";
    }
    $noDItemChecked = true;
}

if (isset($_POST["postDiscounts"])) {
    IntegrationController::PostDiscount($integration, $gfMenu->menu_id);
    $postedDiscount = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'discount');
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
    
    $usable_discounts = array_filter( json_decode($response)->discounts,function($obj){
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
    <label for="inputName" class="col-4 col-form-label">Name</label>
    <div class="col-8">
        <?php
        //var_dump($postedDiscount);
        $d_lid = isset($postedDiscount) && count($postedDiscount) > 0 && isset($postedDiscount[10]->loyverse_id) && $postedDiscount[10]->loyverse_id != "" ? $postedDiscount[10]->name : null;
        ?>
        <span class="form-control" name="inputName" id="inputName"><?php echo isset($d_lid) ? "$d_lid" : "" ?></span>
    </div>
</div>
<div class="mb-3 row">
    <div class="offset-sm-4 col-sm-8">
        <?php
        if (!isset($postedDiscount) && $noDItemChecked == false) {
        ?>
            <form method="post">
                <input type="hidden" name="fetchDiscounts" value="submit" />
                <input type="submit" class="btn btn-primary float-end fs-4" name="fetchMenu" value="Fetch Discount Item" />
            </form>
        <?php } else if (!isset($postedDiscount) && $noDItemChecked == true) { ?>
            <form method="post">
                <input type="hidden" name="postDiscounts" value="submit" />
                <input type="submit" class="btn btn-success float-end fs-4" name="fetchMenu" value="Post Discount Item" />
            </form>
        <?php } else { ?>
        <?php } ?>
    </div>
</div>
<script type="text/javascript">
    //let LDiscounts = JSON.parse(`<?php echo json_encode(fetchDiscounts()) ?>`);
    <?php include "js/promotions.min.js" ?>
</script>
<!--END Integration Extras Promotions Section-->