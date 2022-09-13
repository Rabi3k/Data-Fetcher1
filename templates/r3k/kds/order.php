
<?php
use Src\TableGateways\RequestsGateway;
use Src\Classes\Order;
use Src\Classes\GlobalFunctions;


if(isset($_GET['id']))
{
    $orderId =  $_GET['id'];
}
if(!isset($orderId))
{
    $PageTitle = "Order Not Found!";
    $code =404;
    $httpResponseMessage = $PageTitle;
    include "../$templatePath/head.php";
    include "../$templatePath/header.php";
    include "../$templatePath/error/body.php";
    include "../$templatePath/footer.php";
    exit();
}
$requestGateway = new RequestsGateway($dbConnection);
$datas = $requestGateway->RetriveLastOrderById($orderId);
if(!isset($datas))
{
    $PageTitle = "Order Not Found!";
    $code =404;
    $httpResponseMessage = $PageTitle;
    include "../$templatePath/head.php";
    include "../$templatePath/header.php";
    include "../$templatePath/error/body.php";
    include "../$templatePath/footer.php";
    exit();
}
$OrderClass = new Order($datas);
//echo json_encode($OrderClass->items[0]["id"])."<br/>";
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$PageTitle = "KDS System Order #".$orderId;
include "../$templatePath/head.php";
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<style>
    .legend .row:nth-of-type(even) div {
  background-color:lightgray;
  }
  .legend .row:nth-of-type(odd) div {
  background: #FFFFFF;
  }
</style>
<div id="invoice-POS" class="bg-white p-5">
    
    <center id="top">
      <div class="logo"></div>
      <div class="info"> 
        <h2><?php echo strtoupper($OrderClass->restaurant_name); ?></h2>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
    <hr/>
    <div class="container">
        <div class="row">
            <div class="col-6">
            <h2>Kunder Info</h2>
            <p> 
                Name : <?php echo $OrderClass->client_first_name." ".$OrderClass->client_last_name; ?></br>
                Address : <?php echo $OrderClass->client_address; ?></br>
                Email   : <?php echo $OrderClass->client_email; ?></br>
                Phone   : <?php echo $OrderClass->client_phone; ?></br>
            </p>
            </div>
            <div class="col-6 text-right">
            <h2>Butik Info</h2>
            <p> 
                Address : <?php echo $OrderClass->restaurant_street.", ".$OrderClass->restaurant_zipcode." ".$OrderClass->restaurant_city ; ?></br>
                Phone   : <?php echo $OrderClass->restaurant_phone; ?></br>
            </p>
            </div>
        </div>
    </div>
    <hr/>
    <div id="bot">
        <div id="table" >
            <div class="container">
                <div class="row bg-dark text-light">
                    <div class="col-5 text-left"><h2>Item</h2></div>
                    <div class="col-2 text-center"><h2>Qty</h2></div>
                    <div class="col-5 text-right"><h2>Sub Total</h2></div>
                </div>
                </div>
                <div class="container table legend">
                <?php foreach($OrderClass->items as $item){ 
                    if($item["type"] === 'item'){
                ?>

                <div class="row service">
                    <div class="tableitem col-5 text-left">
                        <span class="itemtext"><?php echo $item["name"] ?></span><br/>
                            <div class="px-5">
                            <?php if($item["instructions"]){?>
                                <span class="itemtext"><?php echo '*'.$item["instructions"].'*' ?></span><br/>
                            <?php } foreach($item["options"] as $option) {?>
                                <span class="itemtext "><?php echo $option["name"] ?></span><br/>
                            <?php } ?>
                            </div>
                    </div>
                    <div class="tableitem col-2 text-center"><span class="itemtext"><?php echo $item["quantity"] ?></span></div>
                    <div class="tableitem col-5 text-right"><span class="itemtext"><?php echo $item["price"]." ".$OrderClass->currency; ?></span></div>
                </div>
                <?php } }?>
                <?php foreach($OrderClass->items as $item){ 
                    if($item["type"] === 'promo_cart'){ ?>
                <div class="row service">
                    <div class="col-6"></div>
                    <div class="Rate col-3 text-right"><h6><?php echo $item["name"] ?></h6></div>
                    <div class="Rate col-3 text-right"><h6><?php echo $item["cart_discount_rate"]; ?></h6></div>
                </div>
                <?php } }?>
                <?php foreach($OrderClass->items as $item){ 
                    if($item["type"] === 'delivery_fee'){ ?>
                <div class="row service">
                    <div class="col-6"></div>
                    <div class="Rate col-3 text-right"><h6><?php echo $item["name"] ?></h6></div>
                    <div class="payment col-3 text-right"><h6><?php echo $item["price"]." ".$OrderClass->currency; ?></h6></div>
                </div>

                <?php } }?>
                <div class="row service"><!--Tax-->
                    <div class="col-6"></div>
                    <div class="Rate col-3 text-right"><h6><?php echo $OrderClass->tax_name; ?></h6></div>
                    <div class="payment col-3 text-right"><h6><?php echo $OrderClass->tax_value." ".$OrderClass->currency; ?></h6></div>
                </div>
                <div class="row tabletitle"><!--Total-->
                    <div class="col-6"></div>
                    <div class="Rate col-3 text-right"><h4>Total</h4></div>
                    <div class="payment col-3 text-right"><h4><?php echo $OrderClass->total_price." ".$OrderClass->currency; ?></h4></div>
                </div>
            </div>
        </div><!--End Table-->
        <hr/>
        <center>
        <div id="legalcopy">
            <img class="border border-dark rounded-circle bg-dark mx-auto d-block" src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2F<?php echo $actual_link?>%2F&choe=UTF-8" title="Link to Google.com" />						
            <p class="legal"><strong>Thank you for your business!</strong></p>
            <h3><?php echo strtoupper($OrderClass->restaurant_name); ?></h3>
        </div></center>

    </div><!--End InvoiceBot-->
  </div><!--End Invoice-->
  </body>
</html>