<?php
if(isset($_GET['rid'])&&(isset($_GET['id']) || isset($_GET['new'])))
{
    include "restaurant/branch-details.php";
}
else{
    include "restaurant/branches-list.php";
}