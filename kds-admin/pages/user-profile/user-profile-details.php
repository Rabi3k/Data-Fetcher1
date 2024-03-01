<?php

use Src\Classes\Profile;
use Src\TableGateways\UserProfilesGateway;

$SaveType = "";
$idUrl = "";
$profilesGateway = new UserProfilesGateway($dbConnection);

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $lprofile = $profilesGateway->GetProfile(intval($_GET['id']))[0];
    $SaveType = "update";
    $idUrl = "id=$lprofile->id";
} else if (isset($_GET['new'])) {
    $lprofile = new Profile();
    $SaveType = "add";
    $idUrl = "new";
}


if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit-details') {

        if (isset($_POST['inputName']) && !empty($_POST['inputName'])) {
            $lprofile->name = $_POST['inputName'];
        }

        if (isset($_POST['profileType']) && !empty($_POST['profileType'])) {
            $lprofile->SetProfileType(strval($_POST['profileType']));
        }

        $lprofile = $profilesGateway->InsertOrUpdate($lprofile);
        $idUrl = "id=$lprofile->id";
    }
}

?>
<div class="row">
    <div class="col-4">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-primary" role="button" href="/admin/user-profiles"><i class="fa-solid fa-circle-chevron-left"></i> <?php _e("back","Back")?></a>
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
        <a class="nav-link" data-toggle="tab" href="#access">Access</a>
    </li>
</ul>

<!-- set User Details Tab -->
<div class="tab-content border border-top-0 p-2" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home">
            <div class="container">
                <div class="row">
                    <div class="form-group col-12">
                        <label for="inputEmail">Name</label>
                        <input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo $lprofile->name ?>" required>
                    </div>
                    <fieldset class="form-group col-md-3">
                        <label>User Type</label>
                        <div class="card p-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="profileType" id="rb_SuperAdmin" value="SuperAdmin">
                                <label class="form-check-label" for="SuperAdmin">
                                    Super Admin
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="profileType" id="rb_Admin" value="Admin">
                                <label class="form-check-label" for="Admin">
                                    Admin
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="profileType" id="rb_User" value="User">
                                <label class="form-check-label" for="User">
                                    User
                                </label>
                            </div>
                        </div>
                    </fieldset>
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
    <div class="tab-pane fade" id="access" role="tabpanel" aria-labelledby="contact-tab">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">select profile access </p>
                </div>
            </div>

            <form method="post" id="profleAccess" action="?<?php echo $idUrl ?>&action=set-access&tab=access">
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
    $("#rb_<?php echo $lprofile->GetProfileType()   ?>").prop("checked", true);
    $("#userDetails").validate();
</script>