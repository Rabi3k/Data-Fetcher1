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


function myFunction () {
    //console.log('Executed!');

var NyActiveOrdersIds=[] ;
 $.getJSON('../api/nyorders').then(r=>
 {
    let toRemove = ActiveOrderIds.filter(x => !r.includes(x));
 let toAdd = r.filter(x => !ActiveOrderIds.includes(x));
 if(toRemove && toRemove.length>0){
    toRemove.forEach(x=>{$('#accordion_'+x).remove(); });
 }
 
 if(toAdd && toAdd.length>0){
    toAdd.forEach(x=>{$.get('create-card.php?id='+x,function(resp){$('#orderCards').append(resp)})});
 }
 ActiveOrderIds = r;
 });
 
 
}

var interval = setInterval( function(){myFunction();},3*1000);
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