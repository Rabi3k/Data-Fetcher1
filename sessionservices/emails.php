<?php

use PHPMailer\PHPMailer\PHPMailer;
use Src\Classes\Options;
use Src\TableGateways\OptionsGateway;

$validator = true;
include "index.php";
if (isset($_GET['q']) && $_GET['q'] != null) {
    $q = strtolower($_GET['q']);
}
EmailsProcessRequest();

function EmailsProcessRequest()
{
    global $dbConnection, $requestMethod, $q, $smtp;

    //var_dump($lOp);
    //$lOp = (new OptionsGateway($dbConnection))->InsertOrUpdate($lOp);
    switch ($requestMethod) {
        case 'POST':
            $body = file_get_contents('php://input');
            //echo $body;

            $smtpPostBody = json_decode($body);
            $retval = (object)array('message' => 'Nothing is here');

            switch ($q) {
                case 'edit-smtp':
                    /*
                        {
                            host = <string>;
                            smtp_auth = <boolval>;
                            username = <string>
                            password = <string>
                            smtp_secure_type = <SecureType>
                            smtp_port = <int>
                        }
                        */
                    //echo json_encode($smtpPostBody);
                    $SMTP_Host = $smtpPostBody->host;
                    $SMTPAuth = boolval($smtpPostBody->smtp_auth);
                    $Username = $smtpPostBody->username;
                    $Password = strval($smtpPostBody->password);
                    $SMTPSecure = $smtpPostBody->smtp_secure_type;
                    $SMTP_Port = intval($smtpPostBody->smtp_port);

                    $smtp['Host'] = $SMTP_Host;
                    $smtp['SMTPAuth'] = $SMTPAuth ? 'true' : 'false';
                    $smtp['SecureType'] = $SMTPSecure;
                    $smtp['Port'] = $SMTP_Port;
                    $smtp['Username'] = $Username;
                    $smtp['Password'] = $Password;

                    $lOp = Options::arrayToClass($smtp, "SMTP");
                    //var_dump($lOp);
                    $lOp = (new OptionsGateway($dbConnection))->InsertOrUpdate($lOp);
                    $retval = (object)array("data" => $lOp);
                    break;
                case 'check-smtp':
                    /*
                        {
                            host = <string>;
                            smtp_auth = <boolval>;
                            username = <string>
                            password = <string>
                            smtp_secure_type = <SecureType>
                            smtp_port = <int>
                        }
                        */
                    //echo json_encode($smtpPostBody);
                    try {
                        $SMTP_Host = $smtpPostBody->host;
                        $SMTPAuth = boolval($smtpPostBody->smtp_auth);
                        $Username = $smtpPostBody->username;
                        $Password = strval($smtpPostBody->password);
                        $SMTPSecure = constant("PHPMailer\\PHPMailer\\PHPMailer::$smtpPostBody->smtp_secure_type");
                        $SMTP_Port = intval($smtpPostBody->smtp_port);

                        $mail = new PHPMailer(true);

                        $mail->isSMTP();                                        //Send using SMTP
                        $mail->Host       =  "$SMTP_Host";                      //Set the SMTP server to send through
                        $mail->SMTPAuth   =  $SMTPAuth;                         //Enable SMTP authentication
                        $mail->Username   = "$Username";                        //SMTP username
                        $mail->Password   = "$Password";                        //SMTP password
                        $mail->SMTPSecure = $SMTPSecure;                        //Enable implicit TLS encryption
                        $mail->Port       = $SMTP_Port;                         //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`


                        $retval = (object)array("can_connect" => $mail->SmtpConnect());
                    } catch (Exception $error) {
                        http_response_code(400);
                        $retval = (object)array(
                            "error" => true,
                            "message" => "PHPMailer Failed to connect : " . $error
                        );
                    }
                    break;
                default:
                    # code...
                    break;
            }
            echo json_encode($retval);
            break;
        case 'GET':
            $liArr = array();
            $retval = (object)array(
                "draw" => 1,
                "recordsTotal" => count($liArr),
                "recordsFiltered" => count($liArr),
                "data" => ($liArr)
            );
            echo json_encode($retval);
            break;
        case 'PUT':
        case 'DELETE':
        default:
            break;
    }
}
