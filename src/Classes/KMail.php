<?php

namespace Src\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class KMail
{
    static function sendResetPasswordMail(User $to, $secretKey)
    {
        
        $secretKey =  bin2hex($secretKey);
        $getWholeUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http:" . "//" . $_SERVER['HTTP_HOST'];
        $mail = new PHPMailer(true);
        try {
             $SMTP_Host = getenv('SMTP_Host');
             $SMTPAuth = boolval(getenv('SMTP_SMTPAuth'));
             $Username =getenv('SMTP_Username');
             $Password = strval(getenv('SMTP_Password'));
             $SMTPSecure = constant('PHPMailer\\PHPMailer\\PHPMailer::'.getenv('SMTP_SMTPSecure'));
             $SMTP_Port = intval(getenv('SMTP_Port'));

            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                //Enable verbose debug output
            $mail->isSMTP();                                        //Send using SMTP
            $mail->Host       =  "$SMTP_Host";                      //Set the SMTP server to send through
            $mail->SMTPAuth   =  $SMTPAuth;                         //Enable SMTP authentication
            $mail->Username   = "$Username";                        //SMTP username
            $mail->Password   = "$Password";                        //SMTP password
            $mail->SMTPSecure = $SMTPSecure;                        //Enable implicit TLS encryption
            $mail->Port       = $SMTP_Port;                         //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('Rabih.kobaissy@outlook.com', 'Relax Kds System');
            $mail->addAddress($to->email, $to->full_name);     //Add a recipient
            $mail->addReplyTo('admin@kbs-leb.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
           // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = "Reset your KDS user password";
            $mail->Body    = "<html>
    <head>
    <title>HTML email</title>
    </head>
    <body>
    <p>This email contains HTML Tags!</p>
    pleasse clicke here to change your password
    <a href='$getWholeUrl/change-password.php?secret=0x$secretKey'>clicke here</a>
    </body>
    </html>";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            //echo 'Message has been sent';
        } catch (Exception $e) {
            $message =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            echo $message;
            $logger = $GLOBALS['logger'];
            $logger->error($message);
        }
    }
}
