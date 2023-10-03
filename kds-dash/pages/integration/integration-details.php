<?php

use Pinq\Traversable;
use Src\Controller\GeneralController;
use Src\Controller\IntegrationController;
use Src\TableGateways\IntegrationGateway;
use Src\TableGateways\OrdersGateway;
use Src\TableGateways\RestaurantsGateway;



if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $integrationGateway = new IntegrationGateway($dbConnection);
    $integration = $integrationGateway->findById($id);
    if ($integration == null) {
        echo " <script> location.href = '/dash/integrations' </script> ";
        exit;
    }
    $restaurant = (new RestaurantsGateway($dbConnection))->FindById($integration->RestaurantId);
    $gfMenu = $integrationGateway->GetGfMenuByRestaurantId($integration->RestaurantId);
    if ($gfMenu == null) {
        $gfMenu = new stdClass();
        $gfMenu->restaurant_id = $restaurant->id;
    }
    $Orders = (new OrdersGateway($dbConnection))->FindByRestaurantRefId($restaurant->gf_refid);
    $promotions = IntegrationController::GetPromotion($integration->gfUid, $integration->Id);
} else {
    echo " <script> location.href = '/dash/integrations' </script> ";
}

if (isset($_POST["fetchMenu"])) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.restaurantlogin.com/api/restaurant/$integration->gfUid/menu?active=true&pictures=true",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $retval = curl_exec($curl);

    curl_close($curl);
    $gfMenu->menu = $retval;


    $gfMenu = $integrationGateway->InsertOrupdateGfMenu($gfMenu->menu, $gfMenu->restaurant_id);
}
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
if (isset($Orders) && count($Orders) > 0) {
    $pOrders = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'order');

    foreach ($Orders as $key => $o) {
        $o->loyverse_id = isset($pOrders[$o->id]) ? $pOrders[$o->id]->loyverse_id : null;

        # code...
    }
}
if (isset($gfMenu->menu) && $gfMenu->menu != null) {
    $gfMenuObj = json_decode($gfMenu->menu);

    $postedElements = $integrationGateway->GetBatchTypeByIntegrationAndMenu($gfMenu->menu_id, $integration->Id,);
    $CatsIds = array_column($gfMenuObj->categories, 'id');
    $aCats = $postedElements['category'];
    $aCatse = filterArrayByKeys($gfMenuObj->categories, ['id', 'name']);
    foreach ($aCatse as  $value) {
        if (array_key_exists($value['id'], $aCats)) {
            $aCats[$value['id']]->hasIssue = false;

            if ($aCats[$value['id']]->name != $value['name']) {
                $aCats[$value['id']]->name = $value['name'];
                $aCats[$value['id']]->hasIssue = true;
            }
        } else {
            $aCats[$value['id']] = (object)array(
                "gf_id" => $value['id'],
                "integration_id" => $integration->Id,
                "type" => "category",
                "gf_menu_id" => $gfMenu->restaurant_id,
                "loyverse_id" => null,
                "name" => $value['name'],
                "hasIssue" => false
            );
        }
    }
    /*,'modifier','option','item','variant'
     */
    $pItems = $postedElements['item'];
    $pModifier = $postedElements['modifier'];
    $pOption = $postedElements['option'];
    $pVariant = $postedElements['variant'];
    $items = array_column($gfMenuObj->categories, 'items');

    $cItems = array();
    foreach ($items as $key => $value) {
        # code...
        foreach ($value as $key2 => $value2) {
            # code...
            $cItems[] = $value2;
        }
    }
    $fItems = array();
    $modifiers = array();
    foreach ($cItems as $key => $value) {
        # code...
        $i = new stdClass();
        $i->id = $value->id;
        $i->name = $value->name;
        $i->description = $value->description;
        $i->gf_category_id = $value->menu_category_id;
        $i->sizes = $value->sizes;
        $i->groups = $value->groups;
        $i->price = $value->price;
        $i->sizese = array_column($value->sizes, 'name');
        $i->sizesNames = implode(" / ", $i->sizese);
        $i->loyverse_id = isset($pItems[$value->id]) ? $pItems[$value->id]->loyverse_id : null;
        $i->picture_hi_res = $value->picture_hi_res;
        $fItems[$value->id] = $i;

        foreach ($value->sizes as $s) {
            $s->loyverse_id = isset($pVariant[$s->id]) ? $pVariant[$s->id]->loyverse_id : null;
            foreach ($s->groups as $g) {
                $g->optionsa = array_column($g->options, 'name');
                $g->optionsNames = implode(" / ", $g->optionsa);
                $g->loyverse_id = isset($pModifier[$g->id]) ? $pModifier[$g->id]->loyverse_id : null;
                if (!in_array($g, $modifiers)) {
                    $modifiers[] = $g;
                }
            }
        }
        foreach ($value->groups as $g) {
            $g->optionsa = array_column($g->options, 'name');
            $g->optionsNames = implode(" / ", $g->optionsa);
            $g->loyverse_id = isset($pModifier[$g->id]) ? $pModifier[$g->id]->loyverse_id : null;
            if (!in_array($g, $modifiers)) {
                $modifiers[] = $g;
            }
        }
    }

    //echo json_encode($cItems);
    $itemsNames = array_column($cItems, 'name');
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

function filterArrayByKeys(array $input, array $column_keys)
{
    global $integration, $integrationGateway;
    $result      = array();
    $column_keys = array_flip($column_keys); // getting keys as values
    foreach ($input as $key => $val) {
        if (!is_array($val)) {
            $val = get_object_vars($val);
        }
        // getting only those key value pairs, which matches $column_keys
        $result[$key] = array_intersect_key($val, $column_keys);
        //$result[$key]["loyverse_id"] = $integrationGateway->GetTypeByIntegrationAndGfId($val['id'], $integration->Id,"category")->loyverse_id;
    }
    return $result;
}


?>
<style>
    .max-list-5 {
        max-height: 20em;
    }

    .form-control.has-issue {
        border-color: var(--bs-yellow);
        padding-right: calc(1.5em + 0.75rem);
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23ffc107" class="bi bi-exclamation-triangle" viewBox="0 0 16 16"><path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/><path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/></svg>');
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .has-issue {
        padding-right: calc(1.5em + 0.75rem);
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23ffc107" class="bi bi-exclamation-triangle" viewBox="0 0 16 16"><path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/><path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/></svg>');
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .is-invalid {
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .is-valid {
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    tr.has-issue {
        border-color: var(--bs-yellow);
    }

    tr.is-invalid {
        border-color: var(--bs-red);
    }

    tr.is-valid {
        border-color: var(--bs-green);
    }

    span.toogle-items.select-all:after {
        content: "Select all";
    }

    span.toogle-items.unselect-all:after {
        content: "Unselect all";
    }
</style>
<div class="container-fluid">
    <center>
        <div class="row">
            <input type="hidden" id="hdfIntegrationId" value="<?php echo $id ?>" />
            <h3>Integration with POS Systems</h3>
        </div>
    </center>
    <hr />

    <!-- integration Inf -->
    <div class="row justify-content-center align-items-center g-2 mb-2">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">restaurant UID</span>
                <input type="text" name="uid" id="txtUid" class="form-control" placeholder="placeholder" value="<?php echo $integration->gfUid ?>">
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">Loyverese Token</span>
                <input type="text" name="uid" id="txtLid" class="form-control" placeholder="placeholder" value="<?php echo $integration->LoyverseToken ?>">
            </div>
        </div>
    </div>
    <!-- Nav tabs -->
    <ul class="nav nav-pills nav-fill navbar-light justify-content-center align-items-center g-2" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark active" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu" type="button" role="tab" aria-controls="menu" aria-selected="true">Menu</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark" id="promotions-tab" data-bs-toggle="tab" data-bs-target="#promotions" type="button" role="tab" aria-controls="promotions" aria-selected="false">Promotions</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button" role="tab" aria-controls="delivery" aria-selected="false">Delivery</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">Orders</button>
        </li>
    </ul>
    <hr />
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Gloria food Menu -->
        <div class="tab-pane active" id="menu" role="tabpanel" aria-labelledby="menu-tab">
            <div class="row justify-content-center align-items-center g-2 mb-2">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="prefixrestaurantId">Restaurnt Id</span>
                        <span class="form-control fs-5 " aria-describedby="prefixrestaurantId">
                            <?php echo $gfMenu->restaurant_id ?>
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="prefixId">Date Updated</span>
                        <span class="form-control fs-5" aria-describedby="prefixId">
                            <!-- <i class="bi bi-exclamation-diamond"></i> -->
                            <?php echo $gfMenu->update_date ?>

                        </span>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="prefixrestaurantId">menu Id</span>
                        <span class="form-control fs-5 " aria-describedby="prefixrestaurantId" id="txtGfMenuId"><?php echo $gfMenu->menu_id ?></span>
                    </div>
                </div>
                <div class="col-2">
                    <form method="post">
                        <input type="hidden" name="fetchMenu" value="submit" />
                        <input type="submit" class="btn btn-primary float-end fs-4" name="fetchMenu" value="Fetch Menu" />
                    </form>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="txtMenu" class="form-label">Menu Details</label>
                        <textarea class="form-control" name="" id="txtMenu" rows="3"><?php echo $gfMenu->menu ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Menu Info -->
            <div class="row justify-content-center align-items-center g-2 mb-2">
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="prefixrestaurantId">Categories</span>
                        <span class="form-control fs-5 " aria-describedby="prefixrestaurantId">
                            <?php echo count($aCats) ?>
                        </span>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="prefixId">Modifiers</span>
                        <span class="form-control fs-5" aria-describedby="prefixId">
                            <?php echo count($modifiers) ?>
                        </span>
                    </div>
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="prefixId">Items</span>
                        <span class="form-control fs-5" aria-describedby="prefixId">
                            <?php echo count($itemsNames) ?>
                        </span>
                    </div>
                </div>
            </div>
            <!-- END Menu Info -->

            <div class="row justify-content-center g-2 ">
                <div class="col-12">
                    <!-- <form method="post"> -->
                    <div class="row justify-content-center align-items-center g-2 ">
                        <div class="col d-grid gap-2 mx-auto">
                            <span class="btn btn-success fs-4" name="postCategories" id="btnPostCategories">Post Categories</span>
                        </div>
                        <div class="col d-grid gap-2 mx-auto">
                            <span class="btn btn-success fs-4" name="postModifiers" id="btnPostModifiers">Post Modifiers</span>
                        </div>
                        <div class="col d-grid gap-2 mx-auto">
                            <span class="btn btn-success fs-4" name="postItems" id="btnPostItems">Post Items</span>
                        </div>

                    </div>
                    <!-- </form> -->
                </div>
                <div class="col-4 ">
                    <div class="d-flex">
                        <div class="p-2 flex-grow-1 text-center"><span class="fs-4">Categories</span></div>
                        <div class="p-2"><span class="toogle-items select-all btn btn-sm btn-info " for-ul="categories"></span></div>
                    </div>
                    <ul class="categories card list-group  overflow-auto max-list-5">
                        <?php foreach ($aCats as $key => $value) { ?>
                            <li class='menu category list-group-item form-control <?php echo isset($value->hasIssue) && $value->hasIssue != false ? "has-issue" : (isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid'  : "is-invalid")  ?>' id="c-<?php echo $value->gf_id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                                <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                <span class="fs-5 fw-bolder"><?php echo $value->name ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-4 ">
                    <div class="d-flex">
                        <div class="p-2 flex-grow-1 text-center"><span class="fs-4">Modifiers</span></div>
                        <div class="p-2"><span class="toogle-items select-all btn btn-sm btn-info " for-ul="modifiers"></span></div>
                    </div>
                    <ul class="modifiers card list-group  overflow-auto max-list-5">
                        <?php foreach ($modifiers as $key => $value) { ?>
                            <li class='menu modifier list-group-item form-control <?php echo isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="m-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                                <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                <span class="fs-5 fw-bolder"><?php echo $value->name ?></span>
                                <ul class="options card list-group  overflow-auto max-list-5">
                                    <?php foreach ($value->options as $o) { ?>
                                        <li class='menu option list-group-item form-control <?php echo isset($pOption[$o->id]) && $pOption[$o->id]->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="m-<?php echo $o->id ?>" lid="<?php echo  $pOption[$o->id]->loyverse_id  ?>" name="<?php echo $o->name ?>" price="<?php echo $o->price ?>">
                                            <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                            <span class="fs-6 fw-semibold"><?php echo $o->name ?> </span>
                                            <span class="fs-6 float-end"><?php echo $o->price ?> DKK</span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-4 ">
                    <div class="d-flex">
                        <div class="p-2 flex-grow-1 text-center"><span class="fs-4">Items</span></div>
                        <div class="p-2"><span class="toogle-items select-all btn btn-sm btn-info " for-ul="items"></span></div>
                    </div>

                    <ul class="items card list-group  overflow-auto max-list-5">
                        <?php foreach ($fItems as $key => $value) { ?>

                            <li class='menu item list-group-item form-control <?php echo isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="i-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>" price="<?php echo $value->price ?>">
                                <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                <span class="fs-5 fw-bolder"><?php echo $value->name ?></span>
                                <span class="fs-6 fw-bolder float-end"><?php echo $value->price ?> DKK</span>

                                <ul class="options card list-group  overflow-auto max-list-5">
                                    <?php foreach ($value->sizes as $o) { ?>
                                        <li class='menu variant list-group-item form-control <?php echo isset($pVariant[$o->id]) && $pVariant[$o->id]->loyverse_id != null ?
                                                                                                    'is-valid' : "is-invalid"   ?>' id="m-<?php echo $o->id ?>" lid="<?php echo  $pVariant[$o->id]->loyverse_id  ?>" name="<?php echo $o->name ?>" price="<?php echo $o->price ?>">
                                            <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                            <span class="fs-6 fw-semibold"><?php echo $o->name ?> </span>
                                            <span class="fs-6 float-end"><?php echo $o->price ?> DKK</span>
                                        </li>
                                    <?php } ?>
                                </ul>

                            <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END  Gloria Food Menu -->
        <!-- Promotions -->
        <div class="tab-pane" id="promotions" role="tabpanel" aria-labelledby="promotions-tab">
            <div class="row justify-content-center g-2 ">
                <div class="col-12">
                    <!-- <form method="post"> -->
                    <div class="row justify-content-center align-items-center g-2 ">
                        <div class="col d-grid gap-2 mx-auto">
                            <span class="btn btn-success fs-4" name="postPromotions" id="btnPostPromotions">Post Promotions</span>
                        </div>
                    </div>
                    <!-- </form> -->
                </div>
                <div class="col">
                    <div class="d-flex">
                        <div class="p-2 flex-grow-1 text-center"><span class="fs-4">Promotions</span></div>
                        <div class="p-2"><span class="toogle-items select-all btn btn-sm btn-info " for-ul="promotions"></span></div>
                    </div>
                    <div class="table-responsive-sm">
                        <table id="tblPromotions" class="table table-striped  w-100">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Outcomes</th>
                                    <th>Udated at</th>
                                    <th>Loyverse Id</th>
                                    <th></th>
                                </tr>
                            <tbody class="promotion">
                            </tbody>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Promotions -->
        <!-- Delivery Fee Item -->
        <div class="tab-pane" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
            <div class="container">

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

            </div>
        </div>
        <!-- END Delivery Fee Item -->
        <!-- Orders -->
        <div class="tab-pane" id="orders" role="tabpanel" aria-labelledby="orders-tab">
            <div class="row justify-content-center g-2 ">
                <div class="col-12">
                    <!-- <form method="post"> -->
                    <div class="row justify-content-center align-items-center g-2 ">
                        <div class="col d-grid gap-2 mx-auto">
                            <span class="btn btn-success fs-4" name="postOrders" id="btnPostOrders">Post Orders</span>
                        </div>
                    </div>
                    <!-- </form> -->
                </div>
                <div class="col">
                    <div class="d-flex">
                        <div class="p-2 flex-grow-1 text-center"><span class="fs-4">Orders</span></div>
                        <div class="p-2"><span class="toogle-items select-all btn btn-sm btn-info " for-ul="orders"></span></div>
                    </div>

                    <div class="table-responsive-sm">
                        <table id="tblOrders" class="table table-striped w-100">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Delivery Fee</th>
                                    <th scope="col">Total Promo Cart Values</th>
                                    <th scope="col">Total Promo Item Values</th>
                                    <th scope="col">loyverse Id</th>
                                    <th scope="col">Validaty</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody class="orders">
                                <?php foreach ($Orders as $key => $order) {
                                    $fmt = numfmt_create('da_DK', NumberFormatter::CURRENCY);
                                    $amount = numfmt_format_currency($fmt, $order->total_price, $order->currency);
                                    $deliveryFee = numfmt_format_currency($fmt, $order->deliveryFee, $order->currency);
                                    $promoItemValues = numfmt_format_currency($fmt, $order->promoItemValues, $order->currency);
                                    $promoCartValues = numfmt_format_currency($fmt, $order->promoCartValues, $order->currency);
                                    $validationClass =  isset($order->hasIssue) && $order->hasIssue != false ? "has-issue" : (isset($order->loyverse_id) && $order->loyverse_id != null ? 'is-valid'  : "is-invalid")
                                ?>
                                    <tr class='menu order <?php echo $validationClass ?>' id="o-<?php echo $order->id ?>" lid="<?php echo $order->loyverse_id  ?>" o-type="<?php echo $order->type ?>">
                                        <td class="fs-5"><?php echo "$order->id" ?></td>
                                        <td class="fs-5"><?php echo "$order->client_first_name $order->client_last_name " ?></td>
                                        <td class="fs-5"><?php echo "$amount" ?></td>
                                        <td class="fs-5"><?php echo "$deliveryFee" ?></td>
                                        <td class="fs-5"><?php echo "$promoCartValues" ?></td>
                                        <td class="fs-5"><?php echo "$promoItemValues" ?></td>
                                        <td class="fs-5"><?php echo $order->loyverse_id ?? null ?></td>

                                        <td><?php echo $validationClass ?></td>
                                        <td class="<?php echo $validationClass ?>">
                                            <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Orders -->
    </div>
    <hr />



</div>
<script type="text/javascript">
    var items = JSON.parse('<?php echo json_encode($fItems) ?>');
    var orders = JSON.parse('<?php echo json_encode($Orders) ?>');

    // DataTables initialisation
    var tblPromotions = $('#tblPromotions').DataTable({

        ajax: {
            url: "/sessionservices/promotions.php?uid=<?php echo $integration->gfUid ?>&iid=<?php echo $integration->Id ?>",
            dataType: 'json',
            type: 'GET',
        },
        //data: jsonfile,
        columns: [{
                data: 'id'
            },
            {
                data: 'name'
            },
            {
                data: 'description'
            },
            {
                data: 'outcomes[0]'
            },
            {
                data: 'updatedAt'

            },
            {
                data: 'loyverseId',
                visible: false

            },
        ],
        columnDefs: [{
            targets: 6,
            visible: true,
            data: function(row, type, val, meta) {
                console.log(type);

                let retval = ' <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>';
                if (row.loyverseId === null) {
                    retval = retval + "<span class='is-invalid'></span>";
                } else {
                    retval = retval + "<span class='is-valid'></span>";
                }
                return retval;

            }
        }],
        createdRow: function(row, data, dataIndex) {
            $(row).addClass("menu promotion");
            if (data.loyverseId === null) {
                $(row).addClass('is-invalid');
            } else {
                $(row).addClass('is-valid');
            }
        }
    });

    var tblOrders = $('#tblOrders').DataTable({
        columnDefs: [{
                target: 0,
                visible: true
            },
            {
                target: 1,
                visible: true
            },
            {
                target: 2,
                visible: true
            },
            {
                target: 3,
                visible: true
            },
            {
                target: 4,
                visible: true
            },
            {
                target: 5,
                visible: true,

            },
            {
                target: 6,
                visible: false
            },
            {
                target: 7,
                visible: false,

            },
            {
                target: 8,
                visible: true,
                searchable: false
            }
        ]
    });
    $("#btnPostOrders").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("tr.order.has-issue,tr.order.is-invalid").each(function(key, value) {
            $(this).find("td>.spinner").toggleClass("visually-hidden");
            $(this).removeClass("has-issue");
            $(this).removeClass("is-invalid");
            $($(this).find("td.is-invalid")).removeClass("is-invalid").addClass("is-none");
            var rowId = $(this).attr("id");
            var rowData = tblOrders.row(this);
            console.log(rowData.data());
            let gfid = $(value).attr("id").substring(2);
            let lid = $(value).attr("lid");
            let name = $(value).attr("name");
            // let data = JSON.stringify({
            //     "integration_id": integrationId,
            //     "gf_menu_id": gfMenuId,
            //     "gf_id": gfid,
            //     "l_id": lid,
            //     "name": name,
            // });

            //console.log(data);
            //PostCategory(data, this);
            $(this).find("td>.spinner").addClass("visually-hidden");
            $(this).addClass("is-valid");
            $($(this).find("td.is-none")).addClass("is-valid");
        });
    });
    $("#btnPostCategories").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("li.category.has-issue,li.category.is-invalid").each(function(key, value) {
            $(this).find(".spinner").toggleClass("visually-hidden");
            $(this).removeClass("has-issue");
            $(this).removeClass("is-invalid");

            let gfid = $(value).attr("id").substring(2);
            let lid = $(value).attr("lid");
            let name = $(value).attr("name");
            let data = JSON.stringify({
                "integration_id": integrationId,
                "gf_menu_id": gfMenuId,
                "gf_id": gfid,
                "l_id": lid,
                "name": name,
            });

            console.log(data);
            PostCategory(data, this);
        })
    });

    $("span.toogle-items").on("click", function() {
        var ulElm = $(this).attr("for-ul");

        var selct = $(this).hasClass("select-all");

        $("." + ulElm + ">.menu").each(function() {
            if (selct) {
                selectMenuItem(this);
            } else {
                unselectMenuItem(this);
            }
        });

        $(this).toggleClass("select-all unselect-all")
    });

    $("li.menu, tr.menu").on("click", function() {
        if (!$(this).hasClass("is-invalid") && !$(this).hasClass("has-issue")) {
            selectMenuItem(this);
        } else {
            unselectMenuItem(this);
        }
    })


    function selectMenuItem(element) {
        if ($(element).hasClass("is-valid")) {
            $(element).addClass("has-issue");
            $(element).removeClass("is-valid");
            $($(element).find("td.is-valid")).removeClass("is-valid").addClass("has-issue");
        } else if (!$(element).hasClass("is-invalid") && !$(element).hasClass("has-issue")) {
            $(element).addClass("is-invalid");
            $($(element).find("td.is-none")).removeClass("is-none").addClass("is-invalid");
            //$(this).removeClass("is-invalid");
        }

    }

    function unselectMenuItem(element) {
        if ($(element).hasClass("has-issue")) {
            $(element).removeClass("has-issue");
            $(element).addClass("is-valid");
            $($(element).find("td.has-issue")).removeClass("has-issue").addClass("is-valid");
        } else if ($(element).hasClass("is-invalid")) {
            //$(this).addClass("has-issue");
            $(element).removeClass("is-invalid");
            $($(element).find("td.is-invalid")).removeClass("is-invalid").addClass("is-none");
        }
    }

    $("#btnPostModifiers").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("li.modifier.has-issue,li.modifier.is-invalid").each(function(key, value) {
            $(this).find(".spinner").toggleClass("visually-hidden");
            $(this).removeClass("has-issue");
            $(this).removeClass("is-invalid");


            let options = [];
            $($(this).find("li.option")).each(function(oKey, oVal) {
                $(this).removeClass("has-issue");
                $(this).removeClass("is-invalid");
                $(this).removeClass("is-valid");
                let gfid = $(oVal).attr("id").substring(2);
                let lid = $(oVal).attr("lid");
                let name = $(oVal).attr("name");
                let price = $(oVal).attr("price");
                options.push({
                    "gf_id": gfid,
                    "l_id": lid,
                    "name": name,
                    "price": parseInt(price)
                })
            })
            let gfid = $(value).attr("id").substring(2);
            let lid = $(value).attr("lid");
            let name = $(value).attr("name");
            let data = JSON.stringify({
                "integration_id": integrationId,
                "gf_menu_id": gfMenuId,
                "gf_id": gfid,
                "l_id": lid,
                "name": name,
                "options": options

            });

            //console.log(data);
            PostModifier(data, this);
        })
    });
    $("#btnPostItems").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("li.item.has-issue,li.item.is-invalid").each(function(key, value) {
            $(this).find(".spinner").toggleClass("visually-hidden");
            $(this).removeClass("has-issue");
            $(this).removeClass("is-invalid");

            let gfid = $(this).attr("id").substring(2);
            $($(this).find("li.variant")).each(function(oKey, oVal) {
                $(this).removeClass("has-issue");
                $(this).removeClass("is-invalid");
                $(this).removeClass("is-valid");

            })
            items[gfid].integration_id = integrationId;
            items[gfid].gf_menu_id = gfMenuId;
            let data = JSON.stringify(items[gfid]);

            //console.log(data);
            PostItem(data, this);
        })
    });


    function PostCategory(data, elem) {
        /*
        integration_id =>hdfIntegrationId
        gf_id
        l_id
        name
        gf_menu_id =>txtGfMenuId
         */
        var settings = {
            "url": "/sessionservices/integration.php?q=postcategory",
            "method": "POST",
            "timeout": 0,
            //"async": true,
            "headers": {
                "Content-Type": "application/json",
            },
            "data": data,
            "success": function(response) {
                console.log(response);
                $(elem).find(".spinner").toggleClass("visually-hidden");
                $(elem).addClass("is-valid");
            }
        };
        $.ajax(settings).fail(function(response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $(elem).addClass("is-invalid");
        });
    }

    function PostItem(data, elem) {
        /*
        integration_id =>hdfIntegrationId
        gf_id
        l_id
        name
        gf_menu_id =>txtGfMenuId
         */
        var settings = {
            "url": "/sessionservices/integration.php?q=postitem",
            "method": "POST",
            "timeout": 0,
            //"async": true,
            "headers": {
                "Content-Type": "application/json",
            },
            "data": data,
            "success": function(response) {
                console.log(response);
                $(elem).find(".spinner").toggleClass("visually-hidden");
                $(elem).addClass("is-valid");
                $(elem).find("li.variant").addClass("is-valid");
            }
        };
        $.ajax(settings).fail(function(response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $(elem).addClass("is-invalid");
            $(elem).find("li.variant").addClass("is-invalid");
        });
    }

    function PostModifier(data, elem) {
        /*
        integration_id =>hdfIntegrationId
        gf_id
        l_id
        name
        gf_menu_id =>txtGfMenuId
         */
        var settings = {
            "url": "/sessionservices/integration.php?q=postmodifier",
            "method": "POST",
            "timeout": 0,
            //"async": true,
            "headers": {
                "Content-Type": "application/json",
            },
            "data": data,
            "success": function(response) {
                console.log(response);
                $(elem).find(".spinner").toggleClass("visually-hidden");
                $(elem).addClass("is-valid");
                $(elem).find("li.option").addClass("is-valid");
            }
        };
        $.ajax(settings).fail(function(response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $(elem).addClass("is-invalid");
            $(elem).find("li.option").addClass("is-invalid");
        });
    }
</script>