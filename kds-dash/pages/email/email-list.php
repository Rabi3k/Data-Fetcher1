<?php

use Src\Classes\KMail;

$mailHeaders = KMail::getMessages($mailbox, $username, $password);

?>
<div class="container-fluid">
    <table class="table table-striped table-hover table-borderless table-info align-middle" id="table-mail">
        <thead class="table-light">
            <tr>
                <th>From</th>
                <th>Subject</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php foreach ($mailHeaders->headers as $key => $head) { ?>
                <tr class='table-info' href="<?php echo "/dash/email-setup/" . trim($head->Msgno) ?>">
                    <td> <?php echo $head->fromaddress ?></td>
                    <td>
                        <?php echo str_replace("_", " ", mb_decode_mimeheader($head->subject)) ?>
                    </td>
                    <td><?php echo date("D d-M-y h:i:s", $head->udate) ?></td>
                </tr>
            <?php }
            ?>
        </tbody>
        <tfoot>

        </tfoot>
    </table>
</div>
<script>
    $(document).ready(function() {
                $tableMail = new DataTable("#table-mail", {
                    responsive: true,
                    order: [
                        [2, 'desc']
                    ],
                    columns: [

                        {
                            target: 0,
                        },
                        {
                            target: 1,

                        },
                        {
                            target: 2,
                            type: "date",
                            //render: DataTable.render.moment( 'Do MMM YYYY' )
                        }
                    ]
                });

                $('#table-mail').on('click', 'tr', function() {
                        window.location = $(this).attr('href');
                        return false;
                    });
                });
</script>