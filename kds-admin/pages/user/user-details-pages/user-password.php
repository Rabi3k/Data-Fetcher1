<div class="container">
    <div class="row text-center">
        <div class="col-12 p-2">
            <p class="text-center h5">Use the form below to change your password.</p>
        </div>
    </div>
    <div class="row text-center">
        <input type="submit" class="btn btn-info btn-load btn-lg" id="btn-resetPass" name="SendRestPaswordEmail" data-loading-text="Sending Email..." value="Send Reset Password Email">
    </div>

    <hr />
    <div class="row text-center">
        <div class="col-12 p-2">
            <p class="text-center h5">Or</p>
        </div>
    </div>
    <hr />
    <form method="post" id="passwordForm" action="?<?php echo $idUrl ?>&action=change-password&tab=password" class="row g-3 needs-validation" novalidate>
        <div class="row">
            <div class="col-9">
                <div class="form-group p-2">
                    <input type="password" class="input-lg form-control" name="password1" id="password1" placeholder="New Password" autocomplete="off" required>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>
                <div class="form-group p-2">
                    <input type="password" class="input-lg form-control" name="password2" id="password2" placeholder="Repeat Password" autocomplete="off">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="row float-end">
                    <div class="col  p-2">
                        <button class="btn btn-info" type="button" id="btn-change-1">
                            <span class="spinner spinner-border spinner-border-sm visually-hidden" role="status" aria-hidden="true"></span>
                            Save
                        </button>
                    </div>
                </div>
            </div>
            <!--/col-sm-6-->
            <div class="col-3">
                <span id="8char" data-feather="x"></span> Between 8 & 20 Characters Long<br>
                <span id="ucase" data-feather="x"></span> One Uppercase Letter<br>
                <span id="lcase" data-feather="x"></span> One Lowercase Letter<br>
                <span id="num" data-feather="x"></span> One Number<br>
                <span id="pwmatch" data-feather="x"></span> Passwords Match
            </div>
        </div>
    </form>

</div>
<script>
    <?php include("js/user-password.min.js"); ?>
</script>