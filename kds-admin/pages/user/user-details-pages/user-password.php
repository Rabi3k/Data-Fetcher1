<?php
$passkey = GeneratePassKey();

?>
<div class="container">
    <div class="row text-center">
        <div class="col-12 p-2">
            <p class="text-center h5"><?php _e("change_password_form_text","Use the form below to change your password.")?></p>
        </div>
    </div>
    <div class="row text-center">
        <button class="btn btn-info btn-load btn-lg" id="btn-resetPass" name="SendRestPaswordEmail" data-loading-text="Sending Email...">
        <?php _e("send_reset_password_email","Send Reset Password Email") ?>
        </button>
    </div>

    <hr />
    <div class="row text-center">
        <div class="col-12 p-2">
            <p class="text-center h5"><?php _e("or","Or")?></p>
        </div>
    </div>
    <hr />
    <form method="post" id="passwordForm" action="?<?php echo $idUrl ?>&action=change-password&tab=password" class="row g-3 needs-validation" novalidate>
        <div class="row">
            <div class="col-9">
                <div class="form-group p-2">
                    <div class="input-group">
                        <div class="input-group-text fs-6 bi bi-eye fs-5 togglePassword" for="password1"></div>
                        <input type="password" class="input-lg form-control" name="password1" id="password1" placeholder="<?php _e("new_password","New Password") ?>" autocomplete="off" required>
                    </div>

                </div>
                <div class="form-group p-2">
                    <div class="input-group">
                        <div class="input-group-text fs-6 bi bi-eye fs-5 togglePassword" for="password2"></div>
                        <input type="password" class="input-lg form-control" name="password2" id="password2" placeholder="<?php _e("repeat_password","Repeat Password") ?>" autocomplete="off">
                    </div>
                </div>
                <div class="row float-end">
                    <div class="col  p-2">
                        <button class="btn btn-info" type="button" id="btn-change-1">
                            <span class="spinner spinner-border spinner-border-sm visually-hidden" role="status" aria-hidden="true"></span>
                            <?php _e("save","Save") ?>
                        </button>
                    </div>
                </div>
            </div>
            <!--/col-sm-6-->
            <div class="col-3">
                <span id="8char" data-feather="x"></span> <?php _e("between_characters_long","Between 8 & 20 Characters Long") ?><br>
                <span id="ucase" data-feather="x"></span> <?php _e("one_uppercase_letter","One Uppercase Letter") ?><br>
                <span id="lcase" data-feather="x"></span> <?php _e("one_lowercase_letter","One Lowercase Letter") ?><br>
                <span id="num" data-feather="x"></span> <?php _e("one_umber","One Number") ?><br>
                <span id="pwmatch" data-feather="x"></span> <?php _e("passwords_match","Passwords Match") ?>
            </div>
        </div>
    </form>
    <hr />
    <div class="row">
        <div class="col-9">
            <div class="form-group p-2">
                <div class="input-group">
                    <div class="input-group-text fs-6 bi bi-eye fs-5 togglePassword" for="passkey"></div>
                    <input type="password" class="input-lg form-control" name="passkey" id="passkey" placeholder="Passkey" autocomplete="off" value="<?php echo str_Decrypt($lUser->passkey) ?>">
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-group p-2">
                <div class="input-group">
                    <?php
                    $uri = str_Decrypt($lUser->passkey);
                    $pattern = '{\w{2}(?<id>\w{4})\w{4}}';

                    if (preg_match($pattern, $uri, $matches)) {
                        echo ("User id: " . hexdec($matches['id']));
                    } else {
                        //echo ("User id: " . dechex(1984));
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="row float-end">
                <div class="col  p-2">
                    <button class="btn btn-info" type="button" id="btn-save-passkey">
                        <span class="spinner spinner-border spinner-border-sm visually-hidden" role="status" aria-hidden="true"></span>
                        <?php _e("generate_new_passkey","Generate new passkey")?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <hr />

</div>
<script>
    <?php include("js/user-password.min.js"); ?>
</script>