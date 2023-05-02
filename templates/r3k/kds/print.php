<?php

use Pinq\Analysis\Functions\Func;
use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;
use Src\Classes\GlobalFunctions;
use Src\Enums\UploadType;

function RedirectToErrorPage($_code, $_message)
{
    global $code, $PageTitle, $httpResponseMessage, $templatePath, $rootpath, $userLogin;


    $httpResponseMessage = $PageTitle = $_message;
    $code = $_code;

    include "../$templatePath/head.php";
    include "../$templatePath/header.php";
    include "../$templatePath/error/body.php";
    include "../$templatePath/footer.php";
    exit();
}
if (isset($_GET['id'])) {
    $orderId =  $_GET['id'];
}
if (!isset($orderId)) {
    RedirectToErrorPage(404, "Order Not Found!");
    exit();
}

$orderstGateway = new OrdersGateway($dbConnection);
$OrderClass = $orderstGateway->FindById($orderId);
//$dataE = (array)end($datas);
//$datasJ = (array)json_decode($dataE,true);
if (!isset($OrderClass) || !isset($OrderClass->id)) {
    RedirectToErrorPage(404, "Order# {$orderId} Not Found!");
    exit();
}
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$PageTitle = "KDS System Order #" . $orderId;
$logoPath = GetImagePath(UploadType::Restaurant,$OrderClass->restaurant_name )
// include "../$templatePath/head.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt example</title>
    <style>
        * {
            font-size: 18px;
            font-family: 'Times New Roman';
        }

        td,
        th,
        tr,
        table {
            border-top: 1px solid black;
            border-collapse: collapse;
            width: 90%;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }


        .border {
            border: 1px solid #dee2e6 !important;
        }

        .border-dark {
            border-color: #343a40 !important;
        }

        td.description,
        th.description {
            width: 100px;
            max-width: 100px;
        }

        td.quantity,
        th.quantity {
            width: 25px;
            max-width: 25px;
            word-break: break-all;
            text-align: start;
        }

        td.price,
        th.price {
            width: 40px;
            max-width: 40px;
            word-break: break-all;
            text-align: end;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: 70mm;
            max-width: 70mm;
        }

        img {
            max-width: inherit;
            width: inherit;
        }

        @media print {

            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }
    </style>

    <head>

    <body>
        <center>
            <div class="ticket">
                <img src="<?php echo $logoPath?>" alt="Logo">
                <h2>Kunde Info</h2>
                <p>
                    Navn : <?php echo $OrderClass->client_first_name . " " . $OrderClass->client_last_name; ?></br>
                    Adresse : <?php echo $OrderClass->client_address; ?></br>
                    E-mail : <?php echo $OrderClass->client_email; ?></br>
                    Mobil nr. : <?php echo $OrderClass->client_phone; ?></br>
                </p>
                <table>
                    <thead>
                        <tr>
                            <th class="quantity">#</th>
                            <th class="description">Beskrivelse</th>
                            <th class="price"><?php echo $OrderClass->currency ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($OrderClass->items as $item) {
                            if ($item->type === 'item') {
                        ?>
                                <tr>
                                    <td class="quantity"><?php echo $item->quantity ?></td>
                                    <td class="description"><?php echo $item->name ?></td>
                                    <td class="price"><?php echo number_format( $item->price, 2, '. ', '' ); ?></td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                    <tfoot>
                        <?php foreach ($OrderClass->items as $item) {
                            if ($item->type === 'promo_item') { ?>
                                <tr>
                                    <td class="quantity"></td>
                                    <td class="description"><?php echo $item->name ?></td>
                                    <td class="price"><?php echo "-" . number_format( $item->item_discount , 2, '. ', '' );?></td>
                                </tr>
                        <?php }
                        } ?>
                        <?php foreach ($OrderClass->items as $item) {
                            if ($item->type === 'promo_cart') { ?>
                                <tr>
                                    <td class="quantity"></td>
                                    <td class="description"><?php echo $item->name ?></td>
                                    <td class="price"><?php echo "-" . number_format( $item->cart_discount_rate, 2, '. ', '' ); ?></td>
                                </tr>
                        <?php }
                        } ?>
                        <?php foreach ($OrderClass->items as $item) {
                            if ($item->type === 'delivery_fee') { ?>
                                <tr>
                                    <td class="quantity"></td>
                                    <td class="description"><?php echo $item->name ?></td>
                                    <td class="price"><?php echo number_format( $item->price, 2, '. ', '' ); ?></td>
                                </tr>
                        <?php }
                        } ?>

                        <tr>
                            <td class="quantity"></td>
                            <td class="description">
                                <h3><?php echo "Total" ?><h3>
                            </td>
                            <td class="price">
                                <h3><b><?php echo number_format( $OrderClass->total_price, 2, '. ', '' ); ?></b></h3>
                            </td>
                        </tr>
                        <tr>
                            <td class="quantity"></td>
                            <td class="description"><?php echo  $OrderClass->tax_name ?></td>
                            <td class="price"><?php echo  number_format( $OrderClass->tax_value , 2, '. ', '' ); ?></td>
                        </tr>
                        <tr>
                            <td class="quantity"></td>
                            <td class="description"></td>
                            <td class="price"></td>
                        </tr>
                    </tfoot>
                </table>
                <p><?php echo $OrderClass->tax_type == "GROSS" ? "! Alle varer er inkl. moms !" : "! Alle varer er eks. moms !" ?></p>
            </div>
            <center>
                <hr />
                <center>
                    <div id="legalcopy">
                        <picture>
                        <img class="border border-dark rounded-circle bg-dark mx-auto d-block" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo urlencode($actual_link) ?>&choe=UTF-8" title="Order #<?php echo $OrderClass->id ?>" />
                        </picture>
                        <p class="legal"><strong>Tak for dit køb!</strong></p>
                        <h3> <?php echo strtoupper($OrderClass->restaurant_name);?></h3>
                        <p>
                            Adresse : torvet 2, 6580 Vamdrup</br>
                            Mobil nr. : +45 22 18 53 35</br>
                        </p>
                    </div>
                </center>
                <button id="btnPrint" class="hidden-print">Print</button>
                <script>
                    const $btnPrint = document.querySelector("#btnPrint");
                    $btnPrint.addEventListener("click", () => {
                        window.print();
                    });
                </script>
    </body>
</html>