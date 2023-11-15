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
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="integrationTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="true">Receipts</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">Messages</button>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                Receipts
            </div>
            <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab"> profile </div>
            <div class="tab-pane" id="messages" role="tabpanel" aria-labelledby="messages-tab"> messages </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row pt-4">
            <div class="col d-flex  justify-content-start">
                <div class="form-group">
                    <label for="min">Fra Dato:</label>
                    <input type="text" id="min" name="min" class="selector">
                </div>

            </div>
            <div class="col d-flex  justify-content-center">
                <div class="form-group">
                    <label for="max">Til Dato:</label>
                    <input type="text" id="max" name="min" class="selector">
                </div>
            </div>
            <div class="col d-flex justify-content-end">
                <button name="reload-btn" id="reload-btn" class="btn btn-primary" role="button"><i class="fa fa-refresh" aria-hidden="true"></i></button>
            </div>
        </div>
        <hr />
        <div class="row">
            <table class="table table-bordered table-striped table-hover" id="receipts-tbl">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Client</th>
                        <th scope="col">Type</th>
                        <th scope="col">Payment Type</th>
                        <th scope="col">moms</th>
                        <th scope="col">Total</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>

    <hr />



</div>
<script type="text/javascript">
    var minDate, maxDate;
    var integrationId = <?= $id ?>

    $.datepicker.setDefaults($.datepicker.regional["da"]);
    $(".selector").datepicker({
        dateFormat: 'yy-mm-dd',
    });
    $(".selector").datepicker("setDate", new Date());
    $(document).ready(function() {});
    $(document).ready(function() {
        // Create date inputs
        minDate = new Date($('#min').val());
        maxDate = new Date($('#max').val());

        // DataTables initialisation
        let table = $("#receipts-tbl").DataTable({
            // dom: 'Blfrtip',
            // buttons: [{
            //     text: 'reload',
            //     action: function(e, dt, node, config) {
            //         reload();
            //     }
            // }],
            /*
            <th scope="col">Id</th>
                  <th scope="col">Client</th>
                  <th scope="col">Type</th>
                  <th scope="col">Payment Type</th>
                  <th scope="col">moms</th>
                  <th scope="col">Total</th>
                  <th scope="col">Date</th>
             */
            dom: '<"container-fluid"<"row"<"col"l><"col align-middle"B><"col"f>>>rtip', //'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            ajax: {
                url: "/dash/sessionservices/Receipts?s=" + moment.utc(minDate).local().format("YYYYMMDD") + "&e=" + moment.utc(maxDate).local().format("YYYYMMDD") + "&i=" + integrationId,
                dataType: 'json',
                type: 'GET',
            },
            //data: jsonfile,
            columns: [{
                    data: 'receipt_number'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return data["order"] ;
                    }

                },
                {
                    data: 'receipt_type'
                },
                {
                    data: 'payments',
                    render: function(data, type, row, meta) {
                       let retval = data.map((x)=> x.name);
                       console.log(retval);

                        return retval;
                    }
                },
                {
                    data: 'total_tax',
                    render: function(data, type, row, meta) {
                        let formatting_options = {
                            style: 'currency',
                            currency: "DKK",
                            minimumFractionDigits: 2,
                        }
                        let currencyString = new Intl.NumberFormat("da-DK", formatting_options);
                        return currencyString.format(data);
                    }
                },
                {
                    data: 'total_money',
                    render: function(data, type, row, meta) {
                        let formatting_options = {
                            style: 'currency',
                            currency: "DKK",
                            minimumFractionDigits: 2,
                        }
                        let currencyString = new Intl.NumberFormat("da-DK", formatting_options);
                        return currencyString.format(data);
                    }
                },
                {
                    data: 'updated_at',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).local().format('YYYY-MM-DD');
                    }
                },
                {
                    data: 'updated_at',
                    render: function(data, type, row, meta) {
                        return moment.utc(data).local().format('HH:mm:ss');
                    }
                },
            ],
            order: [
                [0, 'desc']
            ]
        });

        // Refilter the table
        $('#min, #max').on('change', function() {

            minDate = new Date($('#min').val());
            maxDate = new Date($('#max').val());

            table.ajax.url("/dash/sessionservices/Receipts?s=" + moment.utc(minDate).local().format("YYYYMMDD") + "&e=" + moment.utc(maxDate).local().format("YYYYMMDD") + "&i=" + integrationId).load();
        });
        $('#tblLogs tbody').on('click', 'tr.clickable-row', function() {
            //$(this).toggleClass('selected');
            window.location = $(this).data("href");
        });
        $("#reload-btn").click(function() {
            table.ajax.reload();
        })

    });
</script>