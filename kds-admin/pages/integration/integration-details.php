<?php

use Pinq\Traversable;
use Src\Controller\GeneralController;
use Src\Controller\IntegrationController;
use Src\TableGateways\IntegrationGateway;
use Src\TableGateways\OrdersGateway;
use Src\TableGateways\PaymentRelationGateway;
use Src\TableGateways\RestaurantsGateway;



if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $ids = array_column(($userGateway->GetUser())->restaurants, "id");

    $integrationGateway = new IntegrationGateway($dbConnection);
    $paymentRelationGateway = new PaymentRelationGateway($dbConnection);

    $integration = $integrationGateway->findById($id);
    if ($integration == null || !in_array($integration->RestaurantId, $ids)) {
        echo " <script> location.href = '/admin/integrations' </script> ";
        die;
        //exit;
    }
    $restaurant = (new RestaurantsGateway($dbConnection))->FindById($integration->RestaurantId);

    $Orders = (new OrdersGateway($dbConnection))->FindByRestaurantRefId($restaurant->gf_refid);
} else {
    echo " <script> location.href = '/admin/integrations' </script> ";
    die;
}




if (isset($Orders) && count($Orders) > 0) {
    $pOrders = $integrationGateway->GetBatchTypeByIntegrationAndType($integration->Id, 'order');

    foreach ($Orders as $key => $o) {
        $o->loyverse_id = isset($pOrders[$o->id]) ? $pOrders[$o->id]->loyverse_id : null;

        # code...
    }
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
<div class="container-fluid">
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <center>
                <input type="hidden" id="hdfIntegrationId" value="<?php echo $id ?>" />
                <h3>Integration with POS Systems</h3>
            </center>
        </div>
        <div class="col-1">
            <a class="float-end btn btn-warning" href="/admin/integrations/edit/<?php echo $id ?>">
                <i class="bi bi-pen"></i>
                Edit
            </a>
        </div>
    </div>
    <hr />
    <center>
        <p class="fs-4 fw-bold"><?php echo $restaurant->name ?></p>
    </center>
    <!-- integration Inf -->
    <div class="row justify-content-center align-items-center g-2 mb-2">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">restaurant UID</span>
                <input type="text" name="uid" id="txtUid" class="form-control" placeholder="placeholder" value="<?php echo $integration->gfUid ?>" readonly>
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">Loyverese Token</span>
                <input type="text" name="uid" id="txtLid" class="form-control" placeholder="placeholder" value="<?php echo $integration->LoyverseToken ?>" readonly>
            </div>
        </div>
    </div>
    <!-- Nav tabs -->
    <ul class="nav nav-pills nav-fill navbar-light justify-content-center align-items-center g-2" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark active" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu" type="button" role="tab" aria-controls="menu" aria-selected="true">
                Menu
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark" id="extras-tab" data-bs-toggle="tab" data-bs-target="#extras" type="button" role="tab" aria-controls="extras" aria-selected="false">
                Extras
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">
                Payments
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-outline-dark" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
                Orders
            </button>
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
        <!-- Extras (Promotions / Delivery)-->
        <div class="tab-pane" id="extras" role="tabpanel" aria-labelledby="extras-tab">
            <?php include "integration-details-pages/integration-details-extras.php"; ?>
        </div>
        <!-- END Extras -->
        <!-- Payments -->
        <div class="tab-pane" id="payments" role="tabpanel" aria-labelledby="payments-tab">
            <?php include "integration-details-pages/integration-details-Payments.php"; ?>

        </div>
        <!-- END Payments -->
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
                                    <th scope="col">Type</th>
                                    <th scope="col">Payment</th>
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
                                    $fmt = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

                                    // $amount = numfmt_format_currency($fmt, $order->total_price, $order->currency);
                                    // $deliveryFee = numfmt_format_currency($fmt, $order->deliveryFee, $order->currency);
                                    // $promoItemValues = numfmt_format_currency($fmt, $order->promoItemValues, $order->currency);
                                    // $promoCartValues = numfmt_format_currency($fmt, $order->promoCartValues, $order->currency);
                                    $amount = $fmt->formatCurrency($order->total_price ?? 0, $order->currency);
                                    $deliveryFee = $fmt->formatCurrency($order->deliveryFee ?? 0, $order->currency);
                                    $promoItemValues = $fmt->formatCurrency($order->promoItemValues ?? 0, $order->currency);
                                    $promoCartValues = $fmt->formatCurrency($order->promoCartValues ?? 0, $order->currency);

                                    $validationClass =  isset($order->hasIssue) && $order->hasIssue != false ? "has-issue" : (isset($order->loyverse_id) && $order->loyverse_id != null ? 'is-valid'  : "is-invalid")
                                ?>
                                    <tr class='menu order <?php echo $validationClass ?>' id="o-<?php echo $order->id ?>" lid="<?php echo $order->loyverse_id  ?>" o-type="<?php echo $order->type ?>">
                                        <td class="fs-5"><?php echo "$order->id" ?></td>
                                        <td class="fs-5"><?php echo "$order->client_first_name $order->client_last_name " ?></td>
                                        <td class="fs-5"><?php echo "$order->fulfill_at" ?></td>
                                        <td class="fs-5"><?php echo "$order->fulfill_at" ?></td>
                                        <td class="fs-5"><?php echo "$order->type" ?></td>
                                        <td class="fs-5"><?php echo "$order->payment" ?></td>
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
    // items = JSON.parse('<?php echo json_encode($fItems) ?>');
    var orders = JSON.parse('<?php echo json_encode($Orders) ?>');


    <?php include "integration-details-pages/js/script.min.js"; ?>


    // DataTables initialisation


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
                data: "date",
                render: function(data, type, row) {
                    const CopenhagenDate = getDateByTimezone(new Date(data), 'Europe/Copenhagen', 'da-DK');
                    return CopenhagenDate;
                }
            },
            {
                target: 3,
                visible: true,
                data: "date",
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
                visible: true
            },
            {
                target: 8,
                visible: true
            },
            {
                target: 9,
                visible: true,

            },
            {
                target: 10,
                visible: false
            },
            {
                target: 11,
                visible: false,

            },
            {
                target: 12,
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