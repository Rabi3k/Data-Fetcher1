
<body class="wide bgimg">
<div class="full-div opacity-min bg-danger">
  <div class="display-middle text-white text-center ">
    <span class="text-jumbo">
        Error</span><br/><span class="h3">Code: 
        <?php
        /*if (array_key_exists($code, $codes) && is_numeric($code)) {
            die("Error $code: {$codes[$code]}");
            } else {
            die('Unknown error');
            }*/
            echo $code."<br/> Page ".$httpResponseMessage;
        ?>
    </span>
    
  </div>
  </div>
</body>