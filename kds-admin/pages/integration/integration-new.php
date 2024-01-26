<?php

use Src\Classes\Integration;
use Src\Controller\IntegrationController;
use Src\TableGateways\IntegrationGateway;

$restaurants = ($userGateway->GetUser())->restaurants;
$id = 0;
$RestaurantId = null;
$txtLoyverseToken = "";
$integrationGateway = new IntegrationGateway($dbConnection);
$integration = new Integration();
if (isset($_GET['id'])) {

    $id = $_GET['id'];
    $integration = $integrationGateway->findById($id);
    $txtLoyverseToken = $integration->LoyverseToken;
    $RestaurantId = $integration->RestaurantId;
}

if (isset($_POST['submit'])) {
    $id_uid = explode("¤", $_POST['GloriaFoodUID']);

    $integration->Id = $id;
    $integration->RestaurantId = intval($id_uid[0]);
    $integration->gfUid = $id_uid[1];
    $integration->LoyverseToken = $_POST['LoyverseToken'];
    $integration->StoreId = $_POST['StoreId'];
    $integration->TaxId = $_POST['TaxId'];
    $integration = $integrationGateway->InsertOrupdate($integration);

    //echo "restaurant id: " . $id_uid[0] . ", GloriaFoodUID: " . $id_uid[1] . ", LoyverseToken: " . $_POST['LoyverseToken'];
    echo " <script> location.href = '/admin/integrations/$integration->Id' </script> ";
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-2">
            <a class="btn btn-danger" role="button" href="/admin/integrations/<?php echo $id ?>">
                <i class="fa-solid fa-circle-chevron-left"></i> Back
            </a>
        </div>
        <div class="col-8">
            <center>
                <input type="hidden" id="hdfIntegrationId" value="<?php echo $id ?>" />
                <h3>Integration with POS Systems</h3>

            </center>
        </div>
    </div>

    <hr />
    <div class="row">
        <div class="container">
            <form action="" method="post" class="needs-validation">

                <div class="mb-3 row">
                    <label for="" class="col-4 col-form-label">GloriaFood UID</label>
                    <div class="col-6">
                        <select class="form-select form-select-lg required" name="GloriaFoodUID" id="selectRestaurants">
                            <option <?php isset($RestaurantId) > 0 ? "" : "selected" ?>>Select one</option>
                            <?php
                            foreach ($restaurants as $value) {
                                # code...
                                $selected = isset($RestaurantId) && $value['id'] == $RestaurantId ? "selected" : "";
                                echo "<option value='" . $value['id'] . "¤" . $value['gf_urid'] . "' $selected >" . $value['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="txtLoyverseToken" class="col-4 col-form-label">Loyverse Token</label>
                    <div class="col-6">
                        <input type="text" class="form-control required" name="LoyverseToken" value="<?php echo $txtLoyverseToken ?>" id="txtLoyverseToken" placeholder="Loyverse Token" />
                        <input type="hidden" name="StoreId" id="hdfLoyverseStoreId" placeholder="Loyverse Store Id" />
                        <input type="hidden" name="TaxId" id="hdfLoyverseTaxId" placeholder="Loyverse Store Id" />
                        <div class="invalid-feedback">Token Not Valid</div>
                    </div>
                    <div class="col-2">
                        <span class="btn btn-sm btn-info" id="btnCheckL"> <i class="bi bi-question-circle-fill fs-6"></i> check </span>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="offset-sm-4 col-sm-8">
                        <input type="submit" name="submit" class="btn btn-success float-end mx-1" value="Save" />
                        <a href="/admin/integrations" class="btn btn-secondary float-end mx-1">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict'
        $("#selectRestaurants").on("change", function() {
            let id_Urid = $("#selectRestaurants").val();
            let Urid = (id_Urid.split("¤"))[1];
            var settings = {
                "url": "/sessionservices/restaurants.php?uid=" + Urid,
                "method": "GET",
                "timeout": 0,
            };

            $.ajax(settings).done(function(response) {
                console.log(response.data.Result);
                if (response.data.Result === true) {
                    $("#selectRestaurants").addClass("is-valid");
                    $("#selectRestaurants").removeClass("is-invalid");
                } else {
                    $("#selectRestaurants").addClass("is-invalid");
                    $("#selectRestaurants").removeClass("is-valid");

                }
            });
        });

        $("#btnCheckL").on("click", function() {
            let token = $("#txtLoyverseToken").val();
            var settings = {
                "url": "/sessionservices/restaurants.php?lt=" + token,
                "method": "GET",
                "timeout": 0,
            };

            $.ajax(settings).done(function(response) {
                console.log(response.data.Result);
                if (response.data.Result.valid === true) {
                    $("#txtLoyverseToken").addClass("is-valid");
                    $("#txtLoyverseToken").removeClass("is-invalid");
                    $("#hdfLoyverseStoreId").val(response.data.Result.StoreId);
                    $("#hdfLoyverseTaxId").val(response.data.Result.TaxId);
                } else {
                    $("#txtLoyverseToken").addClass("is-invalid");
                    $("#txtLoyverseToken").removeClass("is-valid");
                    $("#hdfLoyverseStoreId").val("");
                    $("#hdfLoyverseTaxId").val("");
                }


            });
        });
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    let childs = $(form).find(".required")
                    if ($(childs).is(function(index) {
                            return !$(this).hasClass("is-valid")
                        })) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    //form.classList.add('was-validated')
                }, false)
            })
    })()
</script>