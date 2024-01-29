<?php
if(isset($_GET['id']) )
{
    include "company/company-details.php";
}
else if(isset($_GET['q']) && $_GET['q']=="new")
{
    include "company/company-details.php";
}
else{
    include "company/company-list.php";
}