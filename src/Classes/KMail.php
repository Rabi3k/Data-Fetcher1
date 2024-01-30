<?php

namespace Src\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PhpParser\Node\Expr\Cast\Array_;

class KMail
{
    static function sendResetPasswordMail(LoginUser $to, $secretKey)
    {

        $secretKey =  bin2hex($secretKey);
        $getWholeUrl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
        $mail = new PHPMailer(true);
        try {
            $smtp = $GLOBALS['smtp'];


            $SMTP_Host = $smtp['Host'];
            $SMTPAuth = boolval($smtp['SMTPAuth']);
            $Username = $smtp['Username'];
            $Password = strval($smtp['Password']);
            $SMTPSecure = constant('PHPMailer\\PHPMailer\\PHPMailer::' . $smtp['SecureType']);
            $SMTP_Port = intval($smtp['Port']);

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
            $mail->setFrom("$Username", 'Relax Kds System');
            $mail->addAddress($to->email, $to->full_name);     //Add a recipient
            $mail->addReplyTo("$Username", 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = "Reset your KDS user password";
            $mail->Body    =
                "<html>
                    <head>
                        <title>HTML email</title>
                    </head>
                    <body>
                        <p>This email contains HTML Tags!</p>
                            <span>pleasse clicke here to change your password
                                <a href='$getWholeUrl/change-password.php?secret=0x$secretKey'>clicke here</a>
                            </span>
                    </body>
                </html>";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
            //echo 'Message has been sent';
        } catch (Exception $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            $message =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            echo $message;
        }
    }
    public static function getMessage($mailbox, $username, $password, $msg_number)
    {
        try {
            $msg_number1 = $msg_number+1;
            $inbox = imap_open($mailbox, $username, $password)
            or die("can't connect: " . imap_last_error());
            $head = imap_fetch_overview($inbox, "$msg_number:$msg_number", 0);
            $myMail = new MyMail();

            $s = imap_fetchstructure($inbox, $msg_number);
            if (!$s->parts) // Simple message
            {
                $myMail->getpart($inbox, $msg_number, $s, 0);
            } else // Multipart message: cycle through each part
            {
                foreach ($s->parts as $part_n => $p) {
                    $myMail->getpart((object)$inbox, $msg_number, $p, $part_n + 1);
                }
            }
            return (object)array("head"=>$head[0],"message"=>$myMail);
        } catch (Exception $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            $message =  $e->getMessage();
            echo $message;
        }
    }

    public static function getMessages($mailbox, $username, $password, $criteria = "ALL")
    {
        try {

            $inbox = imap_open($mailbox, $username, $password)
                or die("can't connect: " . imap_last_error());

            $emails = imap_search($inbox, 'ALL');
            if ($emails) {
                rsort($emails);
                echo '';

                $headers = array();
                foreach ($emails as $msg_number) {
                    // Get email headers and body
                    $headers[] = imap_headerinfo($inbox, $msg_number);
                }
            }
            imap_close($inbox);
            return (object)array("inbox" => $inbox,"headers" => $headers );
        } catch (Exception $e) {
            (new Loggy())->logy($e->getMessage(), $e->getTraceAsString(), $e);
            $message =  $e->getMessage();
            echo $message;
        }
    }
}
class MyMail
{
    //"Kani-Bal";

    public $attachments = array();
    public $plain_msg = "";
    public $html_msg = "";
    public $charset = "";


    function getpart($mbox, $mid, $p, $part_n)
    {
        $data = ($part_n) ? imap_fetchbody($mbox, $mid, $part_n) : imap_body($mbox, $mid);

        // Decode
        if ($p->encoding == 4) {
            $data = quoted_printable_decode($data);
        } else if ($p->encoding == 3) {
            $data = base64_decode($data);
        }

        // Email Parameters
        $eparams = array();
        if ($p->parameters) {
            foreach ($p->parameters as $x) {
                $eparams[strtolower($x->attribute)] = $x->value;
            }
        }
        if ($p->dparameters) {
            foreach ($p->dparameters as $x) {
                $eparams[strtolower($x->attribute)] = $x->value;
            }
        }

        // Attachments
        if ($eparams['filename'] || $eparams['name']) {
            $filename = ($eparams['filename']) ? $eparams['filename'] : $eparams['name'];
            $this->attachments[$filename] = $data;
        }

        // Text Messaage
        if ($p->type == 0 && $data) {
            if (strtolower($p->subtype) == 'plain') {
                $this->plain_msg .= trim($data) . "\n\n";
            } else {
                $this->html_msg .= $data . '<br><br>';
            }
            $this->charset = $eparams['charset'];
        } else if ($p->type == 2 && $data) {
            $this->plain_msg .= $data . "\n\n";
        }

        // Subparts Recursion
        if ($p->parts) {
            foreach ($p->parts as $part_n2 => $p2) {
                self::getpart($mbox, $mid, $p2, $part_n . '.' . ($part_n2 + 1));
            }
        }
    }
}
enum MyMailCriteria
{
        //ALL - return all messages matching the rest of the criteria
    case ALL;
        //ANSWERED - match messages with the \\ANSWERED flag set
    case ANSWERED;
        // BCC "string" - match messages with "string" in the Bcc: field
    case BCC;
        // BEFORE "date" - match messages with Date: before "date"
    case BEFORE;
        // BODY "string" - match messages with "string" in the body of the message
    case BODY;
        // CC "string" - match messages with "string" in the Cc: field
    case CC;
        // DELETED - match deleted messages
    case DELETED;
        // FLAGGED - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
    case FLAGGED;
        // FROM "string" - match messages with "string" in the From: field
    case FROM;
        // KEYWORD "string" - match messages with "string" as a keyword
    case KEYWORD;
        // NEW - match new messages
    case NEW;
        // OLD - match old messages
    case OLD;
        // ON "date" - match messages with Date: matching "date"
    case ON;
        // RECENT - match messages with the \\RECENT flag set
    case RECENT;
        // SEEN - match messages that have been read (the \\SEEN flag is set)
    case SEEN;
        // SINCE "date" - match messages with Date: after "date"
    case SINCE;
        // SUBJECT "string" - match messages with "string" in the Subject:
    case SUBJECT;
        // TEXT "string" - match messages with text "string"
    case TEXT;
        // TO "string" - match messages with "string" in the To:
    case TO;
        // UNANSWERED - match messages that have not been answered
    case UNANSWERED;
        // UNDELETED - match messages that are not deleted
    case UNDELETED;
        // UNFLAGGED - match messages that are not flagged
    case UNFLAGGED;
        // UNKEYWORD "string" - match messages that do not have the keyword "string"
    case UNKEYWORD;
        // UNSEEN - match messages which have not been read yet
    case UNSEEN;
}
