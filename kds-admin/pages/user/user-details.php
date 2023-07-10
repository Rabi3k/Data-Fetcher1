<?php

use Src\Classes\Company;
use Src\Classes\User;
use Src\Classes\KMail;
use Src\TableGateways\UserLoginGateway;
use Src\TableGateways\UserGateway;
use Src\TableGateways\RestaurantsGateway;
use Src\TableGateways\UserProfilesGateway;

$SaveType = "";
$idUrl = "";
if (isset($_GET['id'])) {
    $lUser = UserGateway::GetUserClass($_GET['id'], false);
    $SaveType = "update";
    $idUrl = "id=$lUser->id";
} else if (isset($_GET['new'])) {
    $lUser = new User();
    $SaveType = "add";
    $idUrl = "new";
}
$profiles = (new UserProfilesGateway($dbConnection))->GetAllProfiles();
$restaurants = (new RestaurantsGateway($dbConnection))->GetAll();
$companiesTree = Company::getAllCompaniesJsonTree();


$userSecret = $userGateway->GetEncryptedKey($lUser->email);
$secretKey =  bin2hex($userSecret);

$compTree = $lUser->GetUserComanyRelationTree();
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'change-password') {
        if (isset($_POST['password1'])) {
            $userGateway->UpdateUserPassword($lUser, $_POST['password1']);
        } else if (array_key_exists('SendRestPaswordEmail', $_POST)) {

            //$secret = $userLogin->GetEncryptedKey($lUser->email);
            KMail::sendResetPasswordMail($lUser, $userSecret);
        }
    } else if ($_GET['action'] == 'edit-details') {

        if (isset($_POST['inputName']) && !empty($_POST['inputName'])) {
            $lUser->full_name = $_POST['inputName'];
        }
        if (isset($_POST['inputUserName']) && !empty($_POST['inputUserName'])) {
            $lUser->user_name = $_POST['inputUserName'];
        }
        if (isset($_POST['inputEmail']) && !empty($_POST['inputEmail'])) {
            $lUser->email = $_POST['inputEmail'];
        }
        // Activate user
        if (isset($_POST['userType']) && !empty($_POST['userType'])) {
            $lUser->SetUsertype(strval($_POST['userType']));
        }
        if (isset($_POST['screenType']) && !empty($_POST['screenType'])) {
            //$lUser->SetUsertype(strval($_POST['userType']));
            switch ($_POST['screenType']) {
                case "OrderDisplay":
                    $lUser->screen_type = 1;
                    break;

                case "ItemDisplay":
                    $lUser->screen_type = 2;
                    break;

                case "CustomerDisplay":
                    $lUser->screen_type = 3;
                    break;

                default:
                    $lUser->screen_type = 1;
                    break;
            }
        }
        if (isset($_POST['inputProfile']) && !empty($_POST['inputProfile'])) {
            $lUser->profile_id = intval($_POST['inputProfile']);
        }
        $lUser = $userGateway->InsertOrUpdate($lUser);
        $idUrl = "id=$lUser->id";
    }
}

if (isset($_POST['set-access'])) {
    $userRelations = array();
    if (isset($_POST['restaurtants'])) {
        foreach ($_POST['restaurtants'] as $key => $val) {
            if (gettype($val) === 'array') {
                foreach ($val as $bkey => $bval) {
                    $ur = new stdClass();
                    $ur->user_id = $lUser->id;
                    $ur->restaurant_id = $key;
                    $ur->company_id = $bkey;
                    array_push($userRelations, $ur);
                }
            } else {
                $ur = new stdClass();
                $ur->user_id = $lUser->id;
                $ur->restaurant_id = $key;
                $ur->company_id = null;
                array_push($userRelations, $ur);
            }
        }
    }

    if (count($userRelations) < 1) {
        $ur = new stdClass();
        $ur->user_id = $lUser->id;
        $ur->restaurant_id = null;
        $ur->company_id = null;
        array_push($userRelations, $ur);
    }
    $userGateway->updateUserRelations($userRelations);
    $lUser = UserGateway::GetUserClass($_GET['id'], false);
}
$companiesTreejs = array();
foreach ($companiesTree as $c) {
    $co = new stdClass();
    $co->parentNodeId = $c->id;
    $co->parentNodeTxt = $c->name;
    $co->childNodes = array();
    foreach ($c->restaurants as $r) {
        $cor = new stdClass();
        $cor->id = $r->id;
        $cor->name = $r->name;
        $co->childNodes[] = $cor;
    }
    $companiesTreejs[] = $co;
}

?>

<div class="row">
    <div class="col-4">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-primary" role="button" href="/admin/users"><i class="fa-solid fa-circle-chevron-left"></i> Back</a>
        </div>
    </div>
    <div class="col-4"></div>
    <div class="col-4 pull-right">
        <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
            <a class="btn btn-primary" role="button" target="_blank" href="/login.php?secret=0x<?php echo $secretKey ?>"><i class="fa fa-solid fa-sign-in"></i>Login with this user</a>
        </div>
    </div>
</div>
<hr />
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">User Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#access">Profile</a>
    </li>
    <li class="nav-item <?php echo  $SaveType === "update" ? "" : "disabled"  ?> ">
        <a class="nav-link" data-toggle="tab" href="#password">Change Password</a>
    </li>
</ul>

<!-- set User Details Tab -->
<div class="tab-content p-2 border border-top-0" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form method="post" id="userDetails" action="?<?php echo $idUrl ?>&action=edit-details&tab=home">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail">Name</label>
                    <input type="text" class="form-control" name="inputName" id="inputName" value="<?php echo $lUser->full_name ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputUserName">User Name</label>
                    <input type="text" class="form-control" name="inputUserName" id="inputUserName" value="<?php echo $lUser->user_name ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputAddress">Email</label>
                <input type="email" class="form-control" name="inputEmail" id="inputEmail" value="<?php echo $lUser->email ?>" required>
            </div>
            <div class="form-row">
                <fieldset class="form-group col-md-3">
                    <label>User Type</label>
                    <div class="card p-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="userType" id="rb_SuperAdmin" value="SuperAdmin">
                            <label class="form-check-label" for="SuperAdmin">
                                Super Admin
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="userType" id="rb_Admin" value="Admin">
                            <label class="form-check-label" for="Admin">
                                Admin
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="userType" id="rb_User" value="User">
                            <label class="form-check-label" for="User">
                                User
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group col-md-5">
                    <label>Screen Type</label>
                    <div class="card p-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="screenType" id="rb_ods" value="OrderDisplay">
                            <label class="form-check-label" for="rb_ods">
                                Order Display
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="screenType" id="rb_ids" value="ItemDisplay">
                            <label class="form-check-label" for="rb_ids">
                                Item Display
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="screenType" id="rb_cds" value="CustomerDisplay">
                            <label class="form-check-label" for="rb_cds">
                                Customer Display
                            </label>
                        </div>
                    </div>
                </fieldset>
                <div class="form-group col-md-4">
                    <label for="inputProfile">Profile</label>
                    <select id="inputProfile" name="inputProfile" class="form-control">

                        <?php foreach ($profiles as $profile) {
                            echo  "<option name='" . $profile->GetProfileType() . "' value='$profile->id'>$profile->name</option>";
                        } ?>
                        <!-- <option name="SuperAdmin" value="1">SA Profile1</option>
                        <option name="SuperAdmin" value="2">SA Profile2</option>
                        <option name="SuperAdmin" value="3">SA Profile3</option>
                        <option name="Admin" value="4">A Profile1</option>
                        <option name="Admin" value="5">A Profile2</option>
                        <option name="Admin" value="6">A Profile3</option>
                        <option name="User"value="7">Super Admin3</option>
                        <option name="User" value="8">Admin3</option>
                        <option name="User"value="9">User3</option> -->
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
    <!-- End of User Details Tab -->
    <!-- set Profifle Tab -->
    <div class="tab-pane fade" id="access" role="tabpanel" aria-labelledby="profile-tab">
        <form method="post" name="setAccess" action="?<?php echo $idUrl ?>&action=set-access&tab=access">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Alias </th>
                        <th>City</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>

            <button type="submit" name="set-access" class="btn btn-primary">Save</button>
        </form>


    </div>
    <!-- End of Profifle Tab -->
    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="contact-tab">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Use the form below to change your password.</p>
                </div>
            </div>
            <form method="post" id="SendResetpasswordForm" action="?<?php echo $idUrl ?>&action=change-password&tab=password">
                <div class="row text-center">
                    <input type="submit" class="btn btn-primary btn-load btn-lg" name="SendRestPaswordEmail" data-loading-text="Sending Email..." value="Send Reset Password Email">
                </div>
            </form>
            <hr />
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Or</p>
                </div>
            </div>
            <hr />
            <form method="post" id="passwordForm" action="?<?php echo $idUrl ?>&action=change-password&tab=password">
                <?php include "user-password.php"
                ?>
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
    $('input:radio[name="userType"]').change(
        function() {
            $('#inputProfile option').hide();
            if ($(this).is(':checked')) {
                $('#inputProfile option[name=' + $(this).val() + ']').show();
                $('#inputProfile').val($('#inputProfile option[name=' + $(this).val() + ']').first().val());
                // append goes here
            }
        });
    $('#inputProfile option').hide();
    $('#inputProfile option[name=<?php echo $lUser->UserType() ?>]').show();
    $("#rb_<?php echo $lUser->UserType()   ?>").prop("checked", true);
    $("#rb_<?php echo $lUser->GetScreenType()   ?>").prop("checked", true);
    $("#inputProfile").val("<?php echo $lUser->Profile->id   ?>");
    $("#userDetails").validate({
        rules: {
            inputUserName: {
                alphanumeric: true
            }
        }
    });
</script>