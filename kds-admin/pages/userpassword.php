<?php

$lUser = $userLogin->GetUser();


?>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#home">User Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#profile">Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#password">Change Password</a>
    </li>
</ul>

<!-- set User Details Tab -->
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form method="post" id="userDetails" action="/admin/userpassword/?action=edit-details&tab=home">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail">Name</label>
                    <input type="text" class="form-control" id="inputName" value="<?php echo $lUser->full_name ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="inputUserName">User Name</label>
                    <input type="text" class="form-control" id="inputUserName" value="<?php echo $lUser->user_name ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputAddress">Email</label>
                <input type="email" class="form-control" id="inputEmail" value="<?php echo $lUser->email ?>">
            </div>

            <fieldset class="form-group">
                <legend class="h6">User Type</legend>
                <div class="card col-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="userType" id="SuperAdmin" value="SuperAdmin">
                        <label class="form-check-label" for="gridRadios1">
                            Super Admin
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="userType" id="Admin" value="Admin">
                        <label class="form-check-label" for="gridRadios2">
                            Admin
                        </label>
                    </div>
                    <div class="form-check disabled">
                        <input class="form-check-input" type="radio" name="userType" id="KitchenUser" value="KitchenUser" disabled>
                        <label class="form-check-label" for="gridRadios3">
                            Kitchen User
                        </label>
                    </div>
                </div>
            </fieldset>

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
            <form method="post" id="passwordForm" action="/admin/userpassword/?action=change-password&tab=password">
                <div class="row">
                    <div class="col-9">
                        <div class="col-12 p-2">
                            <input type="password" class="input-lg form-control" name="password1" id="password1" placeholder="New Password" autocomplete="off">
                        </div>
                        <div class="col-12 p-2">
                            <input type="password" class="input-lg form-control" name="password2" id="password2" placeholder="Repeat Password" autocomplete="off">
                        </div>
                        <div class="offset-9 p-2">
                            <input type="submit" class="btn btn-primary btn-load btn-lg" data-loading-text="Changing Password..." value="Change Password">
                        </div>
                    </div>
                    <!--/col-sm-6-->
                    <div class="col-3">
                        <span id="8char" data-feather="x"></span> 8 Characters Long<br>
                        <span id="ucase" data-feather="x"></span> One Uppercase Letter<br>
                        <span id="lcase" data-feather="x"></span> One Lowercase Letter<br>
                        <span id="num" data-feather="x"></span> One Number<br>
                        <span id="pwmatch" data-feather="x"></span> Passwords Match
                    </div>
                </div>
                <!--/row-->
            </form>
        </div>
    </div>
</div>




<?php
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'change-password') {
        if (isset($_POST['password1'])) {
            $userLogin->UpdateUserPassword($_POST['password1']);
        }
        // Register user
    } else if ($_GET['action'] == 'edit-details') {
        // Activate user
        if(!empty($_POST['userType'])) {
            switch ($_POST['userType']){
                default:
                    echo '  ' . $_POST['userType'];
            }
        } else {
            echo 'Please select the value.';
        }
    }
}
if (isset($_GET['tab'])) {
?>
<script type="text/javascript">
    var val = '<?php echo $_GET['tab'] ?>';
    if(val != ''){
        //alert(val);   
        $(function () {
        $('a[href="#'+val+'"]').addClass('active');
        $('#'+val).addClass('show active');
        });
    }
    $("#<?php echo $_POST['userType'] ?>").prop("checked", true);
</script>
<?php
}else{
    ?>
    <script type="text/javascript">
            //alert(val);   
            $(function () {
            $('a[href="#home"]').addClass('active');
            $('#home').addClass('show active');
            $("#SuperAdmin").prop("checked", true);
            });
    </script>
    <?php
}
?>