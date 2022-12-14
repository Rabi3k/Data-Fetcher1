<!DOCTYPE html>
<html>
<head>
<title><?=$PageTitle?></title>
<link rel="icon" type="image/x-icon" href="/media/System/favicon.ico">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?php echo "/$templatePath" ?>/css/style.min.css">

<!-- <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> -->



<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>


<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script> 

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.12.1/dataRender/datetime.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>



<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>



<script type="text/javascript">
var rootpath = '<?php echo $rootpath ?>';
var templatePath = '<?php echo $templatePath ?>';
var userSecrets = JSON.parse('<?php echo $userLogin->GetSecrets() ?>')
</script>
<script type="text/javascript" src="<?php echo $rootpath."/".$templatePath ?>/js/playsound.min.js"></script>
<script type="text/javascript" src="<?php echo $rootpath."/".$templatePath ?>/js/script.js"></script>

</head>
