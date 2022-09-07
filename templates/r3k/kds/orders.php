<?php
//require "../bootstrap.php";
use Src\TableGateways\RequestsGateway;
if(!$userLogin->checkLogin())
{
    header("Location: $rootpath/login.php");
    exit();
}
?>
<link rel="stylesheet" href="<?php echo $rootpath."/".$templatePath ?>/css/style.css">
<link rel="stylesheet" href="<?php echo $rootpath."/".$templatePath ?>/js/script.js">

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">

<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>


<style>
    .tdbody {
overflow: hidden;
white-space: nowrap;
text-overflow: ellipsis;
-o-text-overflow: ellipsis;
max-width: 20em;
}
</style>
<script>
    $(document).ready(function () {
    $('#example').DataTable();
});
</script>
<?php
include "../$templatePath/header.php";
?>
<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>id</th>
                <th>Client Name</th>
                <th>type</th>
                <th>Updated at</th>
                <th>total_price</th>
            </tr>
        </thead>
        <tbody>
<?php


$requestGateway = new RequestsGateway($dbConnection);
$data = $requestGateway->RetriveAllOrders();
foreach($data as $row)
{
echo
"<tr>
    <td>".$row["id"]."</td>
    <td>".$row["client_first_name"]." ".$row["client_last_name"]."</td>
    <td>".$row["type"]."</td>
    <td>".$row["updated_at"]."</td>
    <td>".$row["total_price"]." ".$row["currency"]."</td>
</tr>";
}
?>
        </tbody>
        <tfoot>
            <tr>
                <th>id</th>
                <th>Private Key</th>
                <th>type</th>
                <th>updated_at Date</th>
                <th>total_price</th>
            </tr>
        </tfoot>
    </table>