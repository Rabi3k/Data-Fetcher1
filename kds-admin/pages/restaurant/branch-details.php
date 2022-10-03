<?php

use Src\Classes\Restaurant;
use Src\Classes\Branch;
use Src\Enums\UploadType;
use Src\TableGateways\RestaurantsGateway;


$SaveType = "";
$idUrl = "";
$restaurantsGateway = new RestaurantsGateway($dbConnection);
$lRestaurant = $restaurantsGateway->GetRestaurant(intval($_GET['rid']))[0];
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $branch = array_filter($lRestaurant->branches, function ($ar) {
        return ($ar->id === intval($_GET['id']));
    });
    $branch = array_values($branch)[0];
    $SaveType = "update";
    $idUrl = "rid=$lRestaurant->id&id=$branch->id";
} else if (isset($_GET['new'])) {
    $branch = new Branch();
    $branch->restaurantId = $lRestaurant->id;
    $SaveType = "add";
    $idUrl = "rid=$lRestaurant->id&new";
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit-details') {
        $anypost = false;
        if (isset($_POST['inputcity']) && !empty($_POST['inputcity'])) {
            $branch->city = $_POST['inputcity'];
            $anypost = true;
        }
        if (isset($_POST['inputZipCode']) && !empty($_POST['inputZipCode'])) {
            $branch->zip_code = $_POST['inputZipCode'];
            $anypost = true;
        }
        if (isset($_POST['inputCountry']) && !empty($_POST['inputCountry'])) {
            $branch->country = $_POST['inputCountry'];
            $anypost = true;
        }
        if (isset($_POST['inputAddress']) && !empty($_POST['inputAddress'])) {
            $branch->address = $_POST['inputAddress'];
            $anypost = true;
        }
        if (isset($_POST['inputPNR']) && !empty($_POST['inputPNR'])) {
            $branch->cvr = $_POST['inputPNR'];
            $anypost = true;
        }
        if (isset($_POST['inputReferenceId']) && !empty($_POST['inputReferenceId'])) {
            $branch->reference_id = $_POST['inputReferenceId'];
            $anypost = true;
        }
        if ($anypost) {
            $branch = $restaurantsGateway->InsertOrUpdateBranch($branch);
            $idUrl = "rid=$lRestaurant->id&id=$branch->id";
            $SaveType = "update";
        }
    } else if ($_GET['action'] == 'set-secrets') {
        $secretList = array();
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, 'secret_')) {
                array_push($secretList, $value);
            }
        }
        if (count($secretList) > 0) {
            $branch->secrets = array();
            $branch->secrets = $secretList;
            $restaurantsGateway->InsertOrUpdateBranchSecrets($branch);
        }
        $idUrl = "rid=$lRestaurant->id&id=$branch->id";
        $SaveType = "update";
    }
}


?>
<div class="row">
    <div class="col-4">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-primary" role="button" href="/admin/restaurants?id=<?php echo $lRestaurant->id ?>"><i class="fa-solid fa-circle-chevron-left"></i> Back</a>
        </div>
    </div>
    <div class="col-4"></div>
    <div class="col-4"></div>
</div>
<hr />
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">Details</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#secrets">Secrets</a>
    </li>
</ul>

<!-- set User Details Tab -->
<div class="tab-content border border-top-0 p-2" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home">
            <div class="container">
                <div class="row">
                    <div class="form-group col-6">
                        <label for="inputcity">city</label>
                        <input type="text" class="form-control" name="inputcity" id="inputcity" value="<?php echo $branch->city ?>" required>
                    </div>
                    <div class="form-group col-3">
                        <label for="inputZipCode ">Zip Code</label>
                        <input type="text" class="form-control" name="inputZipCode" id="inputZipCode" value="<?php echo $branch->zip_code ?>" required>
                    </div>
                    <div class="form-group col-3">
                        <label for="inputCountry">Country</label>
                        <input type="text" class="form-control" name="inputCountry" id="inputCountry" value="<?php echo $branch->country ?>" required>
                    </div>
                    <div class="form-group col-6">
                        <label for="inputAddress">Address</label>
                        <input type="text" class="form-control" name="inputAddress" id="inputAddress" value="<?php echo $branch->address ?>" required>
                    </div>
                    <div class="form-group col-3">
                        <label for="inputPNR">PNR</label>
                        <input type="text" class="form-control" name="inputPNR" id="inputPNR" value="<?php echo $branch->cvr ?>" required>
                    </div>
                    <div class="form-group col-3">
                        <label for="inputReferenceId">Reference Id</label>
                        <input type="text" class="form-control" name="inputReferenceId" id="inputReferenceId" value="<?php echo $branch->reference_id ?>" required>
                    </div>
                    <div class="form-group col-12 text-right float-right">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- End of User Details Tab -->
    <!-- set Profifle Tab -->
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

    </div>
    <!-- End of Profifle Tab -->
    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="secrets" role="tabpanel" aria-labelledby="contact-tab">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Secrets </p>
                </div>
            </div>

            <form method="post" name="setSecrets" id="setSecrets" action="?<?php echo $idUrl ?>&action=set-secrets&tab=secrets">

                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-4"></div>
                    <div class="col-4">
                        <span class="btn btn-primary float-right" id="btNewSecret"><i data-feather='plus-circle'></i>Ny</span>
                    </div>
                </div>
                <hr />
                <div class="table-responsive ">
                    <table class="table table-bordered table-hover" id="tblsecrets">
                        <caption>List of users profiles</caption>
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" id="id">#</th>
                                <th scope="col" id="secret">Secret</th>
                                <th scope="col" id="action"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $secrets = $branch->secrets;
                            $id = 0;
                            foreach ($secrets as $secret) {
                                $id = $id + 1;
                            ?>
                                <tr class='clickable-row' data-href="/admin/branches?rid=<?php echo $lRestaurant->id ?>&id=<?php echo $branch->id ?>">
                                    <td scope="row" class="col-1"><?php echo $id ?></td>
                                    <td scope="row" class="col-10"><input type="text" name="secret_<?php echo $id ?>" id="secret_<?php echo $id ?>" class="form-control" value="<?php echo $secret ?>" /></td>
                                    <td scope="row" class="col-1"><span class="btn btn-danger btn-delete-row" id="btDelete_<?php echo $id ?>"><i data-feather="trash-2"></i></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        var rowcnt = <?php echo $id ?>;
                        var table = $('#tblsecrets').DataTable({
                            responsive: true,
                        });
                        $("#btNewSecret").click(function() {
                            table.row.add({
                                0: ++rowcnt,
                                1: '<input type="text" id="secret_' + rowcnt + '" name="secret_' + rowcnt + '" class="form-control" value="" />',
                                2: '<span class="btn btn-danger btn-delete-row" id="btDelete_"' + rowcnt + '><i data-feather="trash-2"></i></span>',
                            }).draw();
                            feather.replace();
                        });
                        $(".btn-delete-row").click(function() {
                            table.row($(this).parents('tr')).remove().draw();
                        });
                        //table.row.add().draw();
                    });
                </script>
                <div class="row">
                    <div class="form-group col-12 text-right float-right">

                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>




<?php

?>
<script type="text/javascript">
    var val = '<?php echo isset($_GET['tab']) ? $_GET['tab'] : "home" ?>';
    if (val != '') {
        //alert(val);   
        $(function() {
            $('a[href="#' + val + '"]').addClass('active');
            $('#' + val).addClass('show active');
        });
    }
    $("#restaurnatDetails").validate();
</script>