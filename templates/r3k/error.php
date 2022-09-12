<?php
    $code = $_SERVER['REDIRECT_STATUS'];
    $codes = array(
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error'
    );
    $source_url = 'http'.((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    /*if (array_key_exists($code, $codes) && is_numeric($code)) {

       die("Error $code: {$codes[$code]}");
    } else {
       die('Unknown error');
    }*/
?>
<?php
$PageTitle = "KDS System";
include  $templatePath.'/head.php';

include  $templatePath.'/header.php';
?>
<!-- Header / Home-->



<body class="wide bgimg">
<div class="full-div opacity-min bg-secondary">
  <div class="display-middle text-white text-center ">
    <span class="text-jumbo">
        Error</span><br/><span class="h3">Code: 
        <?php
        /*if (array_key_exists($code, $codes) && is_numeric($code)) {
            die("Error $code: {$codes[$code]}");
            } else {
            die('Unknown error');
            }*/
            echo $code."<br/> Page".$codes[$code]??"";
        ?>
    </span>
    
  </div>
  </div>


<?php
include  $templatePath.'/footer.php';
?>
</body>
</html>