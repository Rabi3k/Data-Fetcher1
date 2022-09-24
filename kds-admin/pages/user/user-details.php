<?php

use Src\Classes\KMail;
use Src\TableGateways\UserLoginGateway;
use Src\TableGateways\UserProfilesGateway;
use Src\Classes\User;

$SaveType = "";
$idUrl = "";
if (isset($_GET['id'])) {
    $lUser = UserLoginGateway::GetUserClass($_GET['id'])->GetUser();
    $SaveType = "update";
    $idUrl = "id=$lUser->id";
} else if (isset($_GET['new'])) {
    $lUser = new User();
    $SaveType = "add";
    $idUrl = "new";
}
$profiles = (new UserProfilesGateway($dbConnection))->GetAllProfiles();

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'change-password') {
        if (isset($_POST['password1'])) {
            $userLogin->UpdateUserPassword($lUser, $_POST['password1']);
        } else if (array_key_exists('SendRestPaswordEmail', $_POST)) {

            $secret = $userLogin->GetEncryptedKey($lUser->email);
            KMail::sendResetPasswordMail($lUser, $secret);
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
        if (isset($_POST['inputProfile']) && !empty($_POST['inputProfile'])) {
            $lUser->profile->id = intval($_POST['inputProfile']);
        }
        $lUser = $userLogin->InsertOrUpdate($lUser);
        $idUrl = "id=$lUser->id";
    }
}

?>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">User Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#profile">Profile</a>
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
                <div class="form-group col-md-5"></div>
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
            <script type="text/javascript">

            </script>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
    <!-- End of User Details Tab -->
    <!-- set Profifle Tab -->
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

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
                    <div class="offset-4 p-2">
                        <input type="submit" class="btn btn-primary btn-load btn-lg" name="SendRestPaswordEmail" data-loading-text="Sending Email..." value="Send Reset Password Email">
                    </div>
                </div>
            </form>
            <!-- <hr />
            <div class="row text-center">
                <div class="col-12 p-2">
                    <p class="text-center h5">Or</p>
                </div>
            </div>
            <hr />
            <form method="post" id="passwordForm" action="?<?php echo $idUrl ?>&action=change-password&tab=password">
                <?php //include "user-password.php" 
                ?>
            </form> -->

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
    $("#inputProfile").val("<?php echo $lUser->profile->id   ?>");
    $("#userDetails").validate({
        rules: {
            inputUserName: {
                alphanumeric: true
            }
        }
    });
    
</script>