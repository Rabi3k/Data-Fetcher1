<?php
        $lines = $lines = file($_SERVER["DOCUMENT_ROOT"] . '/logs/app.log');
if(isset($_GET['id']))
{
   include_once "loggy/log-detail.php";
}
else{
   include_once "loggy/logs-list.php";

}

