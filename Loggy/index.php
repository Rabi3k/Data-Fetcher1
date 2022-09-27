<?php
require_once "../bootstrap.php";

use Pinq\Traversable;

$lines = file('../logs/app.log');
$jsonLines = "[" . implode(",", $lines) . "]";
$itemsTr = Traversable::from($lines);

$itemsarray = $itemsTr->select(function ($x) {
    return json_decode($x);
})->asArray();
//echo $data ;
?>

<head>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css" />

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>

</head>

<body>

    <div class="table-responsive ">
        <table class="table table-bordered table-hover" id="tblLogs">
            <thead>
                <tr>
                    <th>message</th>
                    <th>level_name</th>
                    <th>datetime</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itemsarray as $item) { ?>
                    <tr>
                        <td>
                            <?php echo $item->message ?>
                        </td>
                        <td>
                        <?php echo $item->level_name ?>
                        </td>
                        <td>
                        <?php echo $item->datetime ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        var table = $('#tblLogs').DataTable();
    </script>
</body>