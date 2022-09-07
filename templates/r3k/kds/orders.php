<?php
//require "../bootstrap.php";
use Src\TableGateways\RequestsGateway;


if(!$userLogin->checkLogin())
{
    header("Location: $rootpath/login.php");
    exit();
}
include  "../$templatePath/head.php";
?>

<body class="w3-display-container w3-wide bgimg w3-grayscale-min ">
<?php include "../$templatePath/header.php"; ?>
<script>
    $(document).ready(function () {
    $('#example').DataTable();
});
</script>
<!-- content -->
    <div class='bg-white opacity-90' > 
        <center> <h2>List of active orders</h2> </center>
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
<!--End content -->
</div> 
    
<?php
//include  __DIR__.'/footer.php';
?>
</body>
</html>