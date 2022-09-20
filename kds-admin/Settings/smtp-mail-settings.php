<body>
    <center>
        <h2><b>Smtp setup</b></h2>
    </center>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="form-row">
                    <div class="form-group">
                        <label for="SMTPSecure">SMTPSecure Type</label>
                        <select id="SMTPSecure" class="form-control" name="SMTPSecure">
                            <option value='ENCRYPTION_SMTPS'>ssl</option>
                            <option value='ENCRYPTION_STARTTLS'>tls</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php
  $SMTP_Host = getenv('SMTP_Host');
  $SMTPAuth = boolval(getenv('SMTP_SMTPAuth'));
  $Username =getenv('SMTP_Username');
  $Password = strval(getenv('SMTP_Password'));
  $SMTPSecure = constant('PHPMailer\\PHPMailer\\PHPMailer::'.getenv('SMTP_SMTPSecure'));
  $SMTP_Port = intval(getenv('SMTP_Port'));