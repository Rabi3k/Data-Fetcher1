<!-- Integration Extras Delivery Section-->
<?php

use Src\Controller\IntegrationController;


$postedDItem = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'delivery_fee');

$noDItemChecked = false;
if (isset($_POST["fetchDeliveryItem"])) {
    $dItem = fetchDeliveryItem();
    if ($dItem != null) {
        $m_id = $dItem->id;
        $optIds = array();
        foreach ($dItem->variants as $key => $value) {
            $optIds[] = $value->variant_id;
        }


        $l_ids = (object)array(
            "l_id" => $m_id,
            "ol_id" => $optIds
        );
        $respItem = $integrationGateway->InsertOrUpdatePostedType($dItem->item_name, "1", "delivery_fee", $integration->Id, $gfMenu->menu_id, $l_ids->l_id, NULL, $l_ids->ol_id[0]);
        $postedDItem = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'delivery_fee');
    } else {
        echo "No Delivery Fee Item";
    }
    $noDItemChecked = true;
}

if (isset($_POST["postDeliveryItem"])) {
    IntegrationController::PostFeeItem($integration, $gfMenu->menu_id);
    $postedDItem = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'delivery_fee');
    $noDItemChecked = true;
}

function fetchDeliveryItem()
{

    $retval = null;
    $cursor = null;
    do {
        # code...
        $itemsResp = fetchItems($cursor);
        foreach ($itemsResp->items as $key => $value) {
            if ($value->reference_id == "delivery_fee") {
                $retval = $value;
                return $retval;
            }
        }
        $cursor =  isset($itemsResp->cursor) && $itemsResp->cursor != "" ? $itemsResp->cursor : null;
    } while ($cursor != null);
    return $retval;
}
function fetchItems(string $cursor = null)
{
    global $integration;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.loyverse.com/v1.0/items?show_deleted=false&cursor=$cursor&limit=250",
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
    return json_decode($response);
}
?>
<div class="mb-3 row">
    <label for="inputName" class="col-4 col-form-label">Name</label>
    <div class="col-8">
        <?php
        $d_lid = isset($postedDItem) && count($postedDItem) > 0 && isset($postedDItem[1]->loyverse_id) && $postedDItem[1]->loyverse_id != "" ? $postedDItem[1]->name : null;
        ?>
        <span class="form-control" name="inputName" id="inputName"><?php echo isset($d_lid) ? "$d_lid" : "" ?></span>
    </div>
</div>
<div class="mb-3 row">
    <div class="offset-sm-4 col-sm-8">
        <?php
        if (!isset($postedDItem) && $noDItemChecked == false) {
        ?>
            <form method="post">
                <input type="hidden" name="fetchDeliveryItem" value="submit" />
                <input type="submit" class="btn btn-primary float-end fs-4" name="fetchMenu" value="Fetch Delivery Item" />
            </form>
        <?php } else if (!isset($postedDItem) && $noDItemChecked == true) { ?>
            <form method="post">
                <input type="hidden" name="postDeliveryItem" value="submit" />
                <input type="submit" class="btn btn-success float-end fs-4" name="fetchMenu" value="Post Delivery Item" />
            </form>
        <?php } else { ?>
        <?php } ?>
    </div>
</div>
<!--END Integration Extras Delivery Section-->