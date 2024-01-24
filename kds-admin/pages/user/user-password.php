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

            <div class="col p-2">
                <input type="submit" class="btn btn-primary btn-load btn-lg" id="btn-change-password" data-loading-text="Changing Password..." value="Change Password">
            </div>
            <div class="col  p-2">
                <button class="btn btn-primary" type="button" id="btn-change-1">
                    <span class="spinner spinner-border spinner-border-sm visually-hidden" role="status" aria-hidden="true"></span>
                    change
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