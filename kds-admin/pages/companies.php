<?php
if(isset($_GET['id']) || isset($_GET['new']))
{
    include "company/company-details.php";
}
else{
    include "company/company-list.php";
}