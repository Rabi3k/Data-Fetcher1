<?php
$lUser = $userLogin->GetUser();
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Change Password</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <p class="text-center">Use the form below to change your password. Your password cannot be the same as your username.</p>
            <form method="post" id="passwordForm">
                <input type="password" class="input-lg form-control" name="password1" id="password1" placeholder="New Password" autocomplete="off">
                <div class="row">
                    <div class="col-sm-6">
                        <span id="8char" data-feather="x"></span> 8 Characters Long<br>
                        <span id="ucase" data-feather="x"></span> One Uppercase Letter
                    </div>
                    <div class="col-sm-6">
                        <span id="lcase" data-feather="x"></span> One Lowercase Letter<br>
                        <span id="num" data-feather="x"></span> One Number
                    </div>
                </div>
                <input type="password" class="input-lg form-control" name="password2" id="password2" placeholder="Repeat Password" autocomplete="off">
                <div class="row">
                    <div class="col-sm-12">
                        <span id="pwmatch" data-feather="x"></span> Passwords Match
                    </div>
                </div>
                <input type="submit" class="col-xs-12 btn btn-primary btn-load btn-lg" data-loading-text="Changing Password..." value="Change Password">
            </form>
        </div>
        <!--/col-sm-6-->
    </div>
    <!--/row-->
</div>
<?php
 if(isset($_POST['password1']))
 {
    //echo $_POST['password1'];
    $userLogin->UpdateUserPassword($_POST['password1']);
 }
?>