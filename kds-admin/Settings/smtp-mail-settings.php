<?php

use Src\Classes\Options;
use Src\TableGateways\OptionsGateway;

if (isset($_POST['save-smtp'])) {
    if (isset($_POST['inputHost']) && !empty($_POST['inputHost'])) {
        $smtp['Host'] = $_POST['inputHost'];
    }
    if (isset($_POST['inputSMTPAuth']) && !empty($_POST['inputSMTPAuth'])) {
        $smtp['SMTPAuth'] = 'true';
    } else {
        $smtp['SMTPAuth'] = 'false';
    }
    if (isset($_POST['SMTPSecure']) && !empty($_POST['SMTPSecure'])) {
        $smtp['SecureType'] = $_POST['SMTPSecure'];
    }
    if (isset($_POST['inputPort']) && !empty($_POST['inputPort'])) {
        $smtp['Port'] = $_POST['inputPort'];
    }
    if (isset($_POST['inputUsername']) && !empty($_POST['inputUsername'])) {
        $smtp['Username'] = $_POST['inputUsername'];
    }
    if (isset($_POST['inputPassword']) && !empty($_POST['inputPassword'])) {
        $smtp['Password'] = $_POST['inputPassword'];
    }

    $lOp = Options::arrayToClass($smtp, "SMTP");
    //var_dump($lOp);
    $lOp = (new OptionsGateway($dbConnection))->InsertOrUpdate($lOp);
}
?>

<body>
    <center>
        <h2><b>Smtp setup</b></h2>
    </center>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form method="post" action="?action=save-smtp">

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputHost">Host</label>
                            <input type="text" class="form-control" name="inputHost" id="inputHost" value="<?php echo $smtp['Host']; ?>" required>
                        </div>
                        <div class="form-group col-md-2 pt-5">
                            <div class="form-check form-switch ">
                                <input class="form-check-input" type="checkbox" role="switch" id="inputSMTPAuth" name="inputSMTPAuth" <?php echo $smtp['SMTPAuth'] === 'true' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="inputSMTPAuth">SMTPAuth</label>
                            </div>

                        </div>
                        <div class="form-group col-2">
                            <label for="SMTPSecure">SMTPSecure Type</label>
                            <select id="SMTPSecure" name="SMTPSecure" class="form-select">
                                <option value='ENCRYPTION_SMTPS'>ssl</option>
                                <option value='ENCRYPTION_STARTTLS'>tls</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="inputPort">port</label>
                            <input type="text" class="form-control" name="inputPort" id="inputPort" value="<?php echo $smtp['Port']; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputUsername">Username</label>
                            <input type="text" class="form-control" name="inputUsername" id="inputUsername" value="<?php echo $smtp['Username']; ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPassword">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="inputPassword" id="inputPassword" value="<?php echo $smtp['Password']; ?>" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="phs"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="save-smtp" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $("#SMTPSecure").val('<?php echo $smtp['SecureType']; ?>')
        $("#phs").click(function(){
            $(this).find("i").toggleClass("fa-eye fa-eye-slash");
            var attr = $("#inputPassword").attr("type") == "password"? "text":"password";
            $("#inputPassword").attr("type",attr);
        });
    </script>

</body>
<?php
