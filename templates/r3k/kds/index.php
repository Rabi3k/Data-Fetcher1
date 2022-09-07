<?php
//require "../bootstrap.php";
use Src\TableGateways\RequestsGateway;
if(!$userLogin->checkLogin())
{
    header("Location: $rootpath/login.php");
    exit();
}
$PageTitle = "KDS System";
include "../$templatePath/head.php";
?>



<?php
include "../$templatePath/header.php";
?>
<body class="w3-display-container w3-wide bgimg w3-grayscale-min">
<script type='text/javascript'>

$( document ).ready(function() {
    $('.btPrint').click(function(e)
    {
        var tl = $(this).parent().attr('id').replace('accordion_','');
        var pv = {
            'title' : tl,
            'values' : $(this).parent().html()
        };
        PrintElem(pv);
    });
});

function PrintElem(elem)
{
var jObj = (elem);
    if (jObj.title)
    {
        var mywindow = window.open('<?php echo $rootpath ?>/kds/order.php?id='+jObj.title, 'PRINT', 'width=50px');
        /*var mywindow = window.open('', 'PRINT', 'width=50px');

        mywindow.document.write('<html><head><title>' + jObj.title  + '</title>');
        
        mywindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">');
        
        mywindow.document.write('</head><body >');
        mywindow.document.write('<h1>' + jObj.title   + '</h1>');
        mywindow.document.write(jObj.values);
        mywindow.document.write('</body></html>');
*/
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        
        
        setTimeout(() => {
            mywindow.print();
            mywindow.close();
        }, "1000");
        return true;
    }
    return false;
}
function myFunction () {
    //console.log('Executed!');
    $('.card').each(function(){
    var cl= $(this).find('#bgClass').val();
    var date = new Date($(this).find('#OrderDate').val());
    var todayW =new Date(new Date().getTime()+10*60000)
    var today = new Date();
    switch(cl) {
  case 'bg-warning':
    // code block
    if(date<today)
    {
        $(this).toggleClass(cl);
        $(this).find('#bgClass').val('bg-danger');
        $(this).toggleClass('bg-danger');
    }else
    {
        $(this).toggleClass('bg-dark text-light');
    }

    break;
  case 'bg-info':
    if(date<todayW)
    {
        $(this).toggleClass(cl);
        $(this).find('#bgClass').val('bg-warning');
        $(this).toggleClass('bg-warning');
    }
    // code block
    break;
    case 'bg-danger':
    // code block
    $(this).toggleClass('bg-light text-dark');
    break;
  default:
    // code block   
}
                          });
}

var interval = setInterval( function(){myFunction();},1000);
</script>


<div class="card-columns">
<?php
$requestGateway = new RequestsGateway($dbConnection);
$sDate =(new DateTime());
$sDate->setTime(0,0);
$sDate->setTimezone( new \DateTimeZone('Europe/Copenhagen'));

$eDate =(new DateTime());
$eDate->setTime(23,59,59,99);
$eDate->setTimezone( new \DateTimeZone('Europe/Copenhagen'));

$data = $requestGateway->RetriveAllOrdersByDate($sDate,$eDate);
foreach($data as $row)
{
    $OrderDate = new \DateTime($row["fulfill_at"]);
    $OrderDate->setTimezone( new \DateTimeZone($row["restaurant_timezone"]));
    $jDate=$OrderDate->format('Y/m/d h:i:s');
    $oDate= $OrderDate->format('l-M-y  h:i:s');
    $bgClass='bg-info';

    
   /* $todayW = date('d/M/Y h:i:s',strtotime("-10 minutes"));
    $today = date('d/M/Y h:i:s');
    $bgClass='bg-info';
    if($oDate>$todayW)
    {
        $bgClass='bg-info';
    }
    elseif($oDate<$today)
    {
        $bgClass='bg-danger';
    }
    elseif($oDate<$todayW)
    {
        $bgClass='bg-warning';
    }*/
    
    $pvalue = (object) [
        'title' => $row["id"],
        'values' => [
            "<p>Id: ".$row["id"]."<br/>
        Name: ".$row["client_first_name"]." ".$row["client_last_name"]."</p>",
        ]
    ];
    $printValue = json_encode($pvalue);
echo
"
<div id='accordion_".$row["id"]."' class='card opacity-90 $bgClass'>
    <input type='hidden' id='bgClass' value='$bgClass'/>
    <input type='hidden' id='OrderDate' value='$jDate'/>
    <input type='hidden' id='printValue' value='$printValue'/>
    <span class='btPrint bg-secondary text-light' id='print_".$row["id"]."'><i class='fa fa-print' aria-hidden='true'></i></span>

    <div class='card-header row ' data-toggle='collapse' data-target='#collapse_".$row["id"]."' aria-expanded='true' aria-controls='collapseOne'>
            <div class='col-6'>
                <p>Id: ".$row["id"]."<br/>
                Name: ".$row["client_first_name"]." ".$row["client_last_name"]."</p>
            </div>
            <div class='col-6 text-right'>
                <p>Shipment: ".$row["type"]."<br/>
                Payment: ".$row["payment"]."</p>
            </div>
            <div class='col-12'>Til: $oDate</div>
    </div>
    <div id='collapse_".$row["id"]."' class='collapse card-body text-center' aria-labelledby='headingOne' data-parent='#accordion_".$row["id"]."'>
        <ul class='list-group text-white'>";
        foreach($row["items"] as $item)
        {
            if($item["type"] === "item")
            {
                echo "
                <li class='list-group-item card-text list-group-item-secondary d-flex justify-content-between align-items-start'>
                    <div class='ms-2 me-auto fw-bold'>".$item["name"]."</div>
                    
                    <span class='badge bg-secondary text-white rounded-pill'>".$item["quantity"]."</span>
                </li>
                <div class='container'>
                
                    <ul class='list-group'>";
                    if($item["instructions"])
                    {
                        echo  "
                        <li class='list-group-item list-group-item-danger d-flex justify-content-between align-items-start'>
                            <div class='ms-2 me-auto fw-bold'>".$item["instructions"]."</div>
                        </li>";
                    }
                    foreach($item["options"] as $option)
                    {
                        // Group: ".$option["group_name"]." <br/> Value: ".$option["name"]." <br/> quantity: ".$option["quantity"]."
                        echo "
                        <li class='list-group-item list-group-item-primary d-flex justify-content-between align-items-start'>
                            <div class='ms-2 me-auto fw-bold'>".$option["group_name"]."</div>
                            <div class='ms-2 me-auto fw-bold'>".$option["name"]."</div>
                            <span class='badge bg-secondary text-white rounded-pill'>".$option["quantity"]."</span>
                           
                        </li>";
                    }

                echo "</ul></div>";
            }
        }
        echo "</ul>";
        echo "</div>
</div>";
}
?>
</div>
<?php
//include  __DIR__.'/footer.php';
?>
</body>
</html>