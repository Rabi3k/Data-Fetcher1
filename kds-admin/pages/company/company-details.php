<?php

use Src\Classes\Company;
use Src\Enums\UploadType;
use Src\TableGateways\CompanyGateway;


$SaveType = "";
$idUrl = "";
$restaurantsGateway = new CompanyGateway($dbConnection);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $lCompany = $restaurantsGateway->FindById(intval($_GET['id']));
    $SaveType = "update";
    $idUrl = "id=$lCompany->id";
} else if (isset($_GET['new'])) {
    $lCompany = Company::NewCompany();
    $SaveType = "add";
    $idUrl = "new";
    include "company-new.php";
    exit;
}
$companyId = $lCompany->id;
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit-details') {

        if (isset($_POST['inputName']) && !empty($_POST['inputName'])) {
            $lCompany->name = $_POST['inputName'];
        }
        if (isset($_POST['inputEmail']) && !empty($_POST['inputEmail'])) {
            $lCompany->email = $_POST['inputEmail'];
        }
        if (isset($_POST['inputCVR']) && !empty($_POST['inputCVR'])) {
            $lCompany->cvr_nr = $_POST['inputCVR'];
        }
        if (isset($_POST['inputPhone']) && !empty($_POST['inputPhone'])) {
            $lCompany->phone = $_POST['inputPhone'];
        }
        if (isset($_POST['inputStreet']) && !empty($_POST['inputStreet'])) {
            $lCompany->address = $_POST['inputStreet'];
        }
        if (isset($_POST['inputCity']) && !empty($_POST['inputCity'])) {
            $lCompany->city = $_POST['inputCity'];
        }
        if (isset($_POST['inputZip']) && !empty($_POST['inputZip'])) {
            $lCompany->zip = $_POST['inputZip'];
        }
        if (isset($_POST['inputReferenceId']) && !empty($_POST['inputReferenceId'])) {
            $lCompany->gf_refid = $_POST['inputReferenceId'];
        }
        if (isset($_FILES['fileToUpload'])) {
            $nyPath =  UplaodImage(UploadType::Restaurant, $lCompany->name, $_FILES['fileToUpload']);
            $lCompany->logo = (isset($nyPath) && !empty($nyPath)) ? $nyPath : $lCompany->logo;
        }

        $lCompany = $restaurantsGateway->InsertOrUpdate($lCompany);
        $idUrl = "id=$lCompany->id";
        $SaveType = "update";
    }
}
// $logoPath = (isset($lCompany->logo)
//     && !empty($lCompany->logo)
//     && file_exists($_SERVER['DOCUMENT_ROOT'] . $lCompany->logo))
//     ? $lCompany->logo
//     : "/media/restaurant/no-image.png";

?>

<div class="row">
    <div class="col-4">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-primary" role="button" href="/admin/companies"><i class="fa-solid fa-circle-chevron-left"></i> Back</a>
        </div>
    </div>
    <div class="col-4"></div>
    <div class="col-4"></div>
</div>
<hr />
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">Company Info.</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#restaurants">Restaurants</a>
    </li>
</ul>

<!-- set User Details Tab -->
<div class="tab-content border border-top-0 p-2" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home" enctype="multipart/form-data">
            <div class="container">
                <div class="row">
                    <div class="col-9">
                        <div class="form-group">
                            <label for="inputName">Name</label>
                            <input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo $lCompany->name ?>" required>
                            <div class="invalid-feedback">
                                Please provide a valid Name.
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="inputCVR">CVR</label>
                            <input type="text" class="form-control" name="inputCVR" id="inputCVR" value="<?php echo $lCompany->cvr_nr ?>" required>
                            <div class="invalid-feedback">
                                Please provide a valid CVR#.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">E-mail</label>
                        <input type="text" class="form-control" name="inputEmail" id="inputEmail" value="<?php echo $lCompany->email ?>" required>
                    </div>

                    <div class="form-row">

                        <div class="col-4">
                            <label for="inputStreet">address</label>
                            <input type="text" class="form-control" name="inputStreet" id="inputStreet" value="<?php echo $lCompany->address ?>" placeholder="street, bldg. #" required>
                            <div class="invalid-feedback">
                                Please provide a valid state.
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="inputZip">Zip</label>
                            <input type="text" class="form-control" name="inputZip" id="inputZip" value="<?php echo $lCompany->zip ?>" required>
                            <div class="invalid-feedback">
                                Please provide a valid zip.
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="inputCity">City</label>
                            <input type="text" class="form-control" name="inputCity" id="inputCity" value="<?php echo $lCompany->city ?>" required>
                            <div class="invalid-feedback">
                                Please provide a valid city.
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPhone">Phone</label>
                        <input type="text" class="form-control" name="inputPhone" id="inputPhone" value="<?php echo $lCompany->phone ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="inputReferenceId">Reference Id</label>
                        <input type="text" class="form-control" name="inputReferenceId" id="inputReferenceId" value="<?php echo $lCompany->gf_refid ?>" required>
                    </div>
                    <div class="form-group col-12 text-right float-right">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>


            </div>
    </div>
    </form>
    <!-- End of User Details Tab -->
    <!-- set Profifle Tab -->
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

    </div>
    <!-- End of Profifle Tab -->
    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="restaurants" role="tabpanel" aria-labelledby="restaurants-tab">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Restaurants </p>
                </div>
            </div>

            <form method="post" id="profleAccess" action="?<?php echo $idUrl ?>&action=set-restaurants&tab=restaurants">
                <div class="row">
                    <div class="form-group col-12 float-right">
                        <?php include __DIR__ . "/../restaurant/restaurants-list.php" ?>
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