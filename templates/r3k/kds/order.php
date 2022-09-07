<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

<?php
use Src\TableGateways\RequestsGateway;

/*if(!$userLogin->checkLogin())
{
    header("Location: $rootpath/login.php");
    exit();
}*/
$orderId =  $_GET['id'];
if(!$orderId)
{
    die("OrderId is Not Provided");
}
$requestGateway = new RequestsGateway($dbConnection);
$data = $requestGateway->RetriveOrder($orderId)[0];
if(!$data)
{
    die("Order is Not Found");
}
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<div id="invoice-POS" class="bg-white">
    
    <center id="top">
      <div class="logo"></div>
      <div class="info"> 
        <h2><?php echo strtoupper($data["restaurant_name"]); ?></h2>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
    <div class="container">
        <div class="row">
            <div class="col-6">
            <h2>Kunder Info</h2>
            <p> 
                Name : <?php echo $data["client_first_name"]." ".$data["client_last_name"]; ?></br>
                Address : <?php echo $data["client_address"]; ?></br>
                Email   : <?php echo $data["client_email"]; ?></br>
                Phone   : <?php echo $data["client_phone"]; ?></br>
            </p>
            </div>
            <div class="col-6">
            <h2>Butik Info</h2>
            <p> 
                Address : <?php echo $data["restaurant_street"].", ".$data["restaurant_zipcode"]." ".$data["restaurant_city"] ; ?></br>
                Phone   : <?php echo $data["restaurant_phone"]; ?></br>
            </p>
            </div>
        </div>
    </div>
    
    <div id="bot">
        <div id="table">
            <div class="container">
                <div class="row">
                    <div class="col-4"><h2>Item</h2></div>
                    <div class="col-4"><h2>Qty</h2></div>
                    <div class="col-4"><h2>Sub Total</h2></div>
                </div>
                <?php foreach($data["items"] as $item){ 
                    if($item["type"] === 'item'){
                ?>
                <div class="row service">
                    <div class="tableitem col-4">
                        <span class="itemtext"><?php echo $item["name"] ?></span><br/><div class="col-12 text-center">
                    <?php if($item["instructions"]){?>
                        <span class="itemtext"><?php echo $item["instructions"] ?></span><br/>
                    <?php } foreach($item["options"] as $option) {?>
                        <span class="itemtext "><?php echo $option["name"] ?></span><br/>
                    <?php } ?>
                </div></div>
                    <div class="tableitem col-4"><p class="itemtext"><?php echo $item["quantity"] ?></p></div>
                    <div class="tableitem col-4"><p class="itemtext"><?php echo $item["price"] ?></p></div>
                        
                </div>
                <?php } }?>

                <?php foreach($data["items"] as $item){ 
                    if($item["type"] === 'promo_cart'){
                ?>
                <div class="row service">
                    <div class="col-4"></div>
                    <div class="Rate col-4"><h4><?php echo $item["name"] ?></h4></div>
                    <div class="Rate col-4"><h4><?php echo $item["cart_discount_rate"]; ?></h4></div>
                </div>
                <?php } }?>
                <?php foreach($data["items"] as $item){ 
                    if($item["type"] === 'delivery_fee'){
                ?>
                <div class="row service">
                    <div class="col-4"></div>
                    <div class="Rate col-4"><h4><?php echo $item["name"] ?></h4></div>
                    <div class="payment col-4"><h4><?php echo $item["price"]." ".$data["currency"]; ?></h4></div>
                </div>
                <?php } }?>
                
                <div class="row service"><!--Tax-->
                    <div class="col-4"></div>
                    <div class="Rate col-4"><h4><?php echo $data["tax_name"]; ?></h4></div>
                    <div class="payment col-4"><h4><?php echo $data["tax_value"]." ".$data["currency"]; ?></h4></div>
                </div>
                <div class="row tabletitle"><!--Total-->
                    <div class="col-4"></div>
                    <div class="Rate col-4"><h4>Total</h4></div>
                    <div class="payment col-4"><h4><?php echo $data["total_price"]." ".$data["currency"]; ?></h4></div>
                </div>
            </div>
        </div><!--End Table-->
        <center>
        <div id="legalcopy">
            <img class="rounded mx-auto d-block" src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2F<?php echo $actual_link?>%2F&choe=UTF-8" title="Link to Google.com" />						
            <p class="legal"><strong>Thank you for your business!</strong></p>
            <h3><?php echo strtoupper($data["restaurant_name"]); ?></h3>
        </div></center>

    </div><!--End InvoiceBot-->
  </div><!--End Invoice-->
  