<?php

use Src\Classes\Restaurant;
use Src\Enums\UploadType;
use Src\TableGateways\RestaurantsGateway;


$SaveType = "";
$idUrl = "";
$restaurantsGateway = new RestaurantsGateway($dbConnection);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $lRestaurant = $restaurantsGateway->GetRestaurant(intval($_GET['id']))[0];
    $SaveType = "update";
    $idUrl = "id=$lRestaurant->id";
} else if (isset($_GET['new'])) {
    $lRestaurant = new Restaurant();
    $SaveType = "add";
    $idUrl = "new";
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit-details') {

        if (isset($_POST['inputName']) && !empty($_POST['inputName'])) {
            $lRestaurant->name = $_POST['inputName'];
        }
        if (isset($_POST['inputEmail']) && !empty($_POST['inputEmail'])) {
            $lRestaurant->email = $_POST['inputEmail'];
        }
        if (isset($_POST['inputCVR']) && !empty($_POST['inputCVR'])) {
            $lRestaurant->cvr = $_POST['inputCVR'];
        }
        if (isset($_POST['inputPhone']) && !empty($_POST['inputPhone'])) {
            $lRestaurant->phone = $_POST['inputPhone'];
        }
        if (isset($_POST['inputReferenceId']) && !empty($_POST['inputReferenceId'])) {
            $lRestaurant->reference_id = $_POST['inputReferenceId'];
        }
        if (isset($_FILES['fileToUpload'])) {
            $nyPath =  UplaodImage(UploadType::Restaurant, $lRestaurant->name, $_FILES['fileToUpload']);
            $lRestaurant->logo = (isset($nyPath) && !empty($nyPath)) ? $nyPath : $lRestaurant->logo;
        } 


        $lRestaurant = $restaurantsGateway->InsertOrUpdate($lRestaurant);
        $idUrl = "id=$lRestaurant->id";
        $SaveType = "update";
    }
}
$logoPath = (isset($lRestaurant->logo)
    && !empty($lRestaurant->logo)
    && file_exists($_SERVER['DOCUMENT_ROOT'] . $lRestaurant->logo))
    ? $lRestaurant->logo
    : "/media/restaurant/no-image.png";

?>
<div class="row">
    <div class="col-4">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-primary" role="button" href="/admin/restaurants"><i class="fa-solid fa-circle-chevron-left"></i> Back</a>
        </div>
    </div>
    <div class="col-4"></div>
    <div class="col-4"></div>
</div>
<hr />
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">User Details</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#branches">Branches</a>
    </li>
</ul>

<!-- set User Details Tab -->
<div class="tab-content border border-top-0 p-2" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home" enctype="multipart/form-data">
            <div class="container">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="inputName">Title</label>
                            <input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo $lRestaurant->name ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">E-mail</label>
                            <input type="text" class="form-control" name="inputEmail" id="inputEmail" value="<?php echo $lRestaurant->email ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputCVR">CVR</label>
                            <input type="text" class="form-control" name="inputCVR" id="inputCVR" value="<?php echo $lRestaurant->cvr ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputPhone">Phone</label>
                            <input type="text" class="form-control" name="inputPhone" id="inputPhone" value="<?php echo $lRestaurant->phone ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputReferenceId">Reference Id</label>
                            <input type="text" class="form-control" name="inputReferenceId" id="inputReferenceId" value="<?php echo $lRestaurant->reference_id ?>" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col-6">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="fileToUpload" id="fileToUpload" accept="image/svg, image/jpeg, image/jpg, image/png" onchange="readURL(this);">
                                <label class="custom-file-label" for="fileToUpload" >Vælg fil</label>
                            </div>
                        </div>

                        <div class="form-group col-6">
                            <picture>
                                <source srcset="..." type="image/svg+xml, image/jpeg, image/jpg, image/png">
                                <img id="blah" src="<?php echo $logoPath ?>" alt="your image" class="img-thumbnail img-portrait" />
                            </picture>
                        </div>

                        <script>
                            function readURL(input) {
                                if (input.files && input.files[0]) {
                                    var reader = new FileReader();

                                    reader.onload = function(e) {
                                        $('#blah').attr('src', e.target.result);
                                    };

                                    reader.readAsDataURL(input.files[0]);
                                }
                            }
                        </script>
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
    <div class="tab-pane fade" id="branches" role="tabpanel" aria-labelledby="contact-tab">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Branches </p>
                </div>
            </div>

            <form method="post" id="profleAccess" action="?<?php echo $idUrl ?>&action=set-branches&tab=branches">
                <div class="row">
                    <div class="form-group col-12 text-right float-right">
                        <?php include "branches-list.php" ?>
                        <!-- <button type="submit" class="btn btn-primary">Save</button> -->
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