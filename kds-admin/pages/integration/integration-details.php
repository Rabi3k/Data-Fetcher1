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
        echo " <script> location.href = '/admin/integrations' </script> ";
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
    echo " <script> location.href = '/admin/integrations' </script> ";
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
    <?php include "integration-details-pages/css/style.min.css"; ?>
</style>
<br />
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
            <?php include "integration-details-pages/integration-details-menu.php"; ?>
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
                                    <th>Outcomes function</th>
                                    <th>Outcomes discount</th>
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
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
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
                                    //$fmt = numfmt_create('da_DK', NumberFormatter::CURRENCY);
                                    $fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );

                                    // $amount = numfmt_format_currency($fmt, $order->total_price, $order->currency);
                                    // $deliveryFee = numfmt_format_currency($fmt, $order->deliveryFee, $order->currency);
                                    // $promoItemValues = numfmt_format_currency($fmt, $order->promoItemValues, $order->currency);
                                    // $promoCartValues = numfmt_format_currency($fmt, $order->promoCartValues, $order->currency);
                                    $amount = $fmt->formatCurrency( $order->total_price??0, $order->currency);
                                    $deliveryFee = $fmt->formatCurrency( $order->deliveryFee??0, $order->currency);
                                    $promoItemValues = $fmt->formatCurrency( $order->promoItemValues??0, $order->currency);
                                    $promoCartValues = $fmt->formatCurrency( $order->promoCartValues??0, $order->currency);

                                    $validationClass =  isset($order->hasIssue) && $order->hasIssue != false ? "has-issue" : (isset($order->loyverse_id) && $order->loyverse_id != null ? 'is-valid'  : "is-invalid")
                                ?>
                                    <tr class='menu order <?php echo $validationClass ?>' id="o-<?php echo $order->id ?>" lid="<?php echo $order->loyverse_id  ?>" o-type="<?php echo $order->type ?>">
                                        <td class="fs-5"><?php echo "$order->id" ?></td>
                                        <td class="fs-5"><?php echo "$order->client_first_name $order->client_last_name " ?></td>
                                        <td class="fs-5"><?php echo "$order->fulfill_at" ?></td>
                                        <td class="fs-5"><?php echo "$order->fulfill_at" ?></td>
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

   
    <?php include "integration-details-pages/js/script.min.js"; ?>

    
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
                data: 'outcome_config[0].function'
            },
            {
                data: 'outcome_config[0].discounts[0]'
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
            targets: 8,
            visible: true,
            data: function(row, type, val, meta) {
                //console.log(type);

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
                visible: true,
                data:"date",
                render: function(data, type, row) {
                    const CopenhagenDate = getDateByTimezone(new Date(data), 'Europe/Copenhagen', 'da-DK');
                    return CopenhagenDate;
                }
            },
            {
                target: 3,
                visible: true,
                data:"date",
                render: function(data, type, row) {
                    const CopenhagenDate = getTimeByTimezone(new Date(data), 'Europe/Copenhagen', 'da-DK');
                    return CopenhagenDate;
                }
            },
            {
                target: 4,
                visible: true
            },
            {
                target: 5,
                visible: true
            },
            {
                target: 6,
                visible: true
            },
            {
                target: 7,
                visible: true,

            },
            {
                target: 8,
                visible: false
            },
            {
                target: 9,
                visible: false,

            },
            {
                target: 10,
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
    
</script>