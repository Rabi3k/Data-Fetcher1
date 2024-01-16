<!-- Integration Extras Promotions Section-->
<?php

use Src\Controller\IntegrationController;

$promotions = IntegrationController::GetPromotion($integration->gfUid, $integration->Id);
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

    curl_close($curl);
    return json_decode($response)->discounts;
}
?>
<div class="row">
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
            <tbody class="promotion">
            </tbody>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    let LDiscounts = JSON.parse(`<?php echo json_encode(fetchDiscounts()) ?>`);
    <?php include "js/promotions.min.js" ?>
</script>
<!--END Integration Extras Promotions Section-->