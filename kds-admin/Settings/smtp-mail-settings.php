<?php

use Src\Classes\Options;
use Src\TableGateways\OptionsGateway;


?>

<body>
    <center>
        <h2><b>Smtp setup</b></h2>
    </center>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form method="post" class="needs-validation" action="?action=save-smtp">

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
                </form>
                <button type="button" name="check-smtp" class="btn btn-info float-start" id="btn-check-smtp"><i class="bi bi-check-circle"></i> Check values</button>
                <button type="button" name="save-smtp" class="btn btn-info float-end disabled" id="btn-save-smtp"><i class="bi bi-save"></i> Save</button>
            </div>
        </div>
    </div>
    <script>
        $("#SMTPSecure").val('<?php echo $smtp['SecureType']; ?>');
        let smtpVals = {
            "host": $(inputHost).val(),
            "smtp_auth": $(inputSMTPAuth).val(),
            "username": $(inputUsername).val(),
            "password": $(inputPassword).val(),
            "smtp_secure_type": $(SMTPSecure).val(),
            "smtp_port": $(inputPort).val()
        };
        $("#btn-save-smtp").click(function(e) {
            smtpVals = {
                "host": $(inputHost).val(),
                "smtp_auth": $(inputSMTPAuth).val(),
                "username": $(inputUsername).val(),
                "password": $(inputPassword).val(),
                "smtp_secure_type": $(SMTPSecure).val(),
                "smtp_port": $(inputPort).val()
            };
            var settings = {
                "url": "/sessionservices/emails.php?q=edit-smtp",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify(smtpVals),
                "error": function(response) {
                    showAlert(response.responseJSON.message,true)
                    console.log(response);
                }
            };

            $.ajax(settings).done(function(response) {
                showAlert("SMTP values saved!");
            });
        });
        $("#btn-check-smtp").click(function(e) {
            smtpVals = {
                "host": $(inputHost).val(),
                "smtp_auth": $(inputSMTPAuth).val(),
                "username": $(inputUsername).val(),
                "password": $(inputPassword).val(),
                "smtp_secure_type": $(SMTPSecure).val(),
                "smtp_port": $(inputPort).val()
            };
            var settings = {
                "url": "/sessionservices/emails.php?q=check-smtp",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify(smtpVals),
                "error": function(response) {
                    showAlert(response.responseJSON.message,true)
                    console.log(response);
                }
            };

            $.ajax(settings).done(function(response) {
                showAlert("Values are valid");
                $("#btn-save-smtp").removeClass("disabled");
            });
        });

        $("#phs").click(function() {
            $(this).find("i").toggleClass("fa-eye fa-eye-slash");
            var attr = $("#inputPassword").attr("type") == "password" ? "text" : "password";
            $("#inputPassword").attr("type", attr);
        });
    </script>

</body>
<?php
