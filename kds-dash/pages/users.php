<?php
if(isset($_GET['id']) || isset($_GET['new']))
{
    include "user/user-details.php";
}
else{
    include "user/users-list.php";
}