<?php

use Src\Classes\Restaurant;
use Src\Enums\UploadType;
use Src\TableGateways\RestaurantsGateway;


$SaveType = "";
$idUrl = "";
$restaurantsGateway = new RestaurantsGateway($dbConnection);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $lRestaurant = $restaurantsGateway->FindById(intval($_GET['id']));
    $companyId = $lRestaurant->company_id;
    $SaveType = "update";
    $idUrl = "id=$lRestaurant->id";
} else if (isset($_GET['new'])) {
    $companyId = $_GET['cid'];
    $lRestaurant = Restaurant::NewRestaurant();
    $lRestaurant->company_id =  $companyId;
    $SaveType = "add";
    $idUrl = "new=&cid=$lRestaurant->company_id";
}
/*
{
		"id": null,
		"name": null,
		"alias": null,
		"email": null,
		"phone": null,
		"address": null,
		"post_nr": null,
		"city": null,
		"country": null,
		"p_nr": null,
		"company_id": null,
		"is_gf": null,
		"gf_urid": null,
		"gf_refid": null,
		"is_managed": null,
		"gf_cdn_base_path": null
	}  */
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit-details') {

        if (isset($_POST['inputName']) && !empty($_POST['inputName'])) {
            $lRestaurant->name = $_POST['inputName'];
        }
        if (isset($_POST['inputAlias']) && !empty($_POST['inputAlias'])) {
            $lRestaurant->alias = $_POST['inputAlias'];
        }
        if (isset($_POST['inputEmail']) && !empty($_POST['inputEmail'])) {
            $lRestaurant->email = $_POST['inputEmail'];
        }
        if (isset($_POST['inputPnr']) && !empty($_POST['inputPnr'])) {
            $lRestaurant->p_nr = $_POST['inputPnr'];
        }
        if (isset($_POST['inputPhone']) && !empty($_POST['inputPhone'])) {
            $lRestaurant->phone = $_POST['inputPhone'];
        }
        if (isset($_POST['inputStreet']) && !empty($_POST['inputStreet'])) {
            $lRestaurant->address = $_POST['inputStreet'];
        }
        if (isset($_POST['inputZip']) && !empty($_POST['inputZip'])) {
            $lRestaurant->post_nr = $_POST['inputZip'];
        }
        if (isset($_POST['inputCity']) && !empty($_POST['inputCity'])) {
            $lRestaurant->city = $_POST['inputCity'];
        }
        if (isset($_POST['inputReferenceId']) && !empty($_POST['inputReferenceId'])) {
            $lRestaurant->gf_refid = $_POST['inputReferenceId'];
            $lRestaurant->is_managed = true;
            $lRestaurant->is_gf = true;
        }
        if (isset($_POST['inputReferenceUId']) && !empty($_POST['inputReferenceUId'])) {
            $lRestaurant->gf_urid = $_POST['inputReferenceUId'];
        }
        if (isset($_POST['inputCdnPath']) && !empty($_POST['inputCdnPath'])) {
            $lRestaurant->gf_cdn_base_path = $_POST['inputCdnPath'];
        }
        if (isset($_FILES['fileToUpload'])) {
            $nyPath =  UplaodImage(UploadType::Restaurant, $lRestaurant->name, $_FILES['fileToUpload']);
            if($nyPath["upload"] == 0)
            {
                $errorMsg = $nyPath["message"];
            }
            //$lRestaurant->logo = (isset($nyPath) && !empty($nyPath)) ? $nyPath : $lRestaurant->logo;
        }


        $lRestaurant = $restaurantsGateway->InsertOrUpdate($lRestaurant);
        $idUrl = "id=$lRestaurant->id";
        $SaveType = "update";
    }
}
$logoPath = GetImagePath(UploadType::Restaurant, $lRestaurant->name);

?>
<div class="alert alert-success" role="alert" hidden>
  
</div>
<div class="alert alert-danger" role="alert" <?php echo (isset($errorMsg) &&!empty($errorMsg))?"":"hidden"  ?>>
 <?php echo $errorMsg ?>
</div>
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
        <a class="nav-link" data-toggle="tab" href="#home">restaurant info</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#reference">Reference info</a>
    </li>
    <li>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modelImporter">
            Import From server
        </button>

        <!-- Modal -->
        <div class="modal fade" id="modelImporter" tabindex="-1" role="dialog" aria-labelledby="Restaurant Importer" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Restaurant Importer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="form-group">
                                <label for="inputUrid">Restaurant UID</label>
                                <input type="text" class="form-control" name="inputUrid" id="inputUrid" value="<?php echo $lRestaurant->gf_urid ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn-close-modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="btn-import-restaurant" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
            <script>
                $("#btn-import-restaurant").click(function() {
                    var Urid = $("#inputUrid").val();
                    var settings = {
                        "url": "/api/restaurant/" + Urid,
                        "method": "GET",
                        "timeout": 0,
                    };

                    $.ajax(settings).done(function(response) {
                        console.log(response);
                        resp = response.data;
                        $("#inputName").val(resp.name);
                        $("#inputPhone").val(resp.phones);
                        $("#inputStreet").val(resp.street);
                        $("#inputCity").val(resp.city);
                        $("#inputZip").val(resp.zipcode);
                        $("#inputReferenceId").val(resp.id);
                        $("#inputReferenceUId").val(Urid);
                        $("#inputCdnPath").val(resp.cdn_base_path);
                        $("#btn-close-modal").click();
                    });
                });
            </script>
        </div>


    </li>
</ul>
<!-- {
		"id": null,
		"name": null,
		"alias": null,
		"email": null,
		"phone": null,
		"address": null,
		"post_nr": null,
		"city": null,
		"country": null,
		"p_nr": null,
		"company_id": null,
		"is_gf": null,
		"gf_urid": null,
		"gf_refid": null,
		"is_managed": null,
		"gf_cdn_base_path": null
	} -->
<!-- set home Tab -->
<form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home" enctype="multipart/form-data">
<div class="tab-content border border-top-0 p-2" id="myTabContent">

        <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="container">
                <div class="row">
                    <div class="form-group">
                        <label for="inputName">name</label>
                        <input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo $lRestaurant->name ?>" required>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="inputAlias">alias</label>
                            <input type="text" class="form-control" name="inputAlias" id="inputAlias" value="<?php echo $lRestaurant->alias ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail">E-mail</label>
                            <input type="text" class="form-control" name="inputEmail" id="inputEmail" value="<?php echo $lRestaurant->email ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputPnr">PNumber</label>
                            <input type="text" class="form-control" name="inputPnr" id="inputPnr" value="<?php echo $lRestaurant->p_nr ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputPhone">Phone</label>
                            <input type="text" class="form-control" name="inputPhone" id="inputPhone" value="<?php echo $lRestaurant->phone ?>" required>
                        </div>
                        <div class="form-row">

                            <div class="col-4">
                                <label for="inputStreet">address</label>
                                <input type="text" class="form-control" name="inputStreet" id="inputStreet" value="<?php echo $lRestaurant->address ?>" required>
                                <div class="invalid-feedback">
                                    Please provide a valid street.
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="inputZip">Zip</label>
                                <input type="text" class="form-control" name="inputZip" id="inputZip" value="<?php echo $lRestaurant->post_nr ?>" required>
                                <div class="invalid-feedback">
                                    Please provide a valid zip.
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="inputCity">City</label>
                                <input type="text" class="form-control" name="inputCity" id="inputCity" value="<?php echo $lRestaurant->city ?>" required>
                                <div class="invalid-feedback">
                                    Please provide a valid city.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group col-6">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="fileToUpload" id="fileToUpload" accept="image/svg, image/jpeg, image/jpg, image/png" onchange="readURL(this);">
                                <label class="custom-file-label" for="fileToUpload">Vælg fil</label>
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


                </div>
            </div>

        </div>
        <!-- End of home Tab -->
        <!-- set reference Tab -->
        <div class="tab-pane fade" id="reference" role="tabpanel" aria-labelledby="reference-tab">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="inputReferenceId">Reference Id</label>
                            <input type="text" class="form-control" name="inputReferenceId" id="inputReferenceId" value="<?php echo $lRestaurant->gf_refid ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputReferenceUId">Reference UId</label>
                            <input type="text" class="form-control" name="inputReferenceUId" id="inputReferenceUId" value="<?php echo $lRestaurant->gf_urid ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="inputCdnPath">Reference cdn path</label>
                            <input type="text" class="form-control" name="inputCdnPath" id="inputCdnPath" value="<?php echo $lRestaurant->gf_cdn_base_path ?>" required>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- End of reference Tab -->
        
    </div>
    <div class="form-group col-12 text-right float-right">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>




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