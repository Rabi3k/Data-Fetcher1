<?php

use Src\Classes\KMail;

$mailHeaders = KMail::getMessages($mailbox, $username, $password);

?>
<div class="container-fluid">
    <table class="table table-striped table-hover table-borderless table-info align-middle" id="table-mail">
        <thead class="table-light">
            <tr>
                <th><?php echo T_("from")?></th>
                <th><?php echo _("email")?></th>
                <th><?php _("Subject")?></th>
                <th><?php _("Date")?></th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php foreach ($mailHeaders->headers as $key => $head) { 
                //echo json_encode($head);
                ?>

                <tr class='table-info' href="<?php echo "/dash/email-setup/" . trim($head->Msgno) ?>">
                    <td> <?php echo $head->fromaddress ?></td>
                    <td> <?php echo $head->from[0]->mailbox."@".$head->from[0]->host ?></td>
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
                    dom: '<"container-fluid pt-2"<"row"<"col"l><"col d-flex justify-content-center"f><"col d-flex justify-content-end">>>rtip',//'rfBltip',
                    order: [
                        [3, 'desc']
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

                        },
                        {
                            target: 3,
                            type: "date",
                            //render: DataTable.render.moment( 'Do MMM YYYY' )
                        }
                    ]
                });

                $('#table-mail').on('click', 'tr', function() {
                    if($(this).parents("thead").length<1)
                    {
                        window.location = $(this).attr('href');
                        return false;

                    }
                    });
                });
</script>