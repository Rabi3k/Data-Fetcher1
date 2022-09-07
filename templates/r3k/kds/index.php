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

                          });
                          $.ajax({
  type: 'post',
  url: 'load-cards.php',
  data: {
    sDate:'<?php  $sDate ?>',
    eDate:'<?php  $eDate ?>',
  },
  success: function (response) {
   // We get the element having id of display_info and put the response inside it
   $( '#display_info' ).html(response);
   $('#orderCards').html(response);
  }
  });
}

var interval = setInterval( function(){myFunction();},1000);
</script>


<div class="card-columns" id="orderCards">
<?php


include "load-cards.php";
?>

</div>
<?php
//include  __DIR__.'/footer.php';
?>
</body>
</html>