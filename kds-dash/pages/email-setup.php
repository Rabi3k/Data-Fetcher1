<?php

// Fetch an overview for all messages in INBOX

//$result = imap_fetchheader($mbox,"1:{$MC->Nmsgs}",0);

//echo "" . json_encode($emails) . "";
// foreach ($result as $overview) {

//     echo "<li>".json_encode($overview)."</li>";
//     //"#{$overview->msgno} ({$overview->date}) - From: {$overview->from}    {$overview->subject}<br/>";
// }


$mailbox = "{mail.funneat.dk:993/imap/ssl/novalidate-cert}INBOX"; //{mail.funneat.dk:143}INBOX
$username = "rko@funneat.dk"; //"abv@funneat.dk";
$password = "Rabih1984";

if (isset($_GET['id'])) {
    $msgId=intval($_GET['id']);
    include("email/email-details.php");
} else {
    include("email/email-list.php");
}


