<?php

use Src\Classes\KMail;

$mailDetail = KMail::getMessage($mailbox, $username, $password, $msgId);
//var_dump($mailDetail->head);

?>
<header class="row g-0">
    <div class="col-1">
        <a name="" id="" class="btn btn-primary" href="/dash/email-setup" role="button">
            <i class="bi bi-arrow-left-circle"></i> back
        </a>
    </div>
    <div class="col text-center">
        <h5>
            <strong>
                <?php echo $mailDetail->head->from ?>
            </strong>
        </h5><br/>
        <h6>
            <?php echo str_replace("_", " ", mb_decode_mimeheader($mailDetail->head->subject)) ?>
        </h6>
    </div>
    <div class="col-1">

    </div>
</header>
<hr />
<div class="container-fluid">

    <div class="row my-3">
        <div class="col">
            <?php echo $mailDetail->message->html_msg  ?>
        </div>
    </div>

</div>