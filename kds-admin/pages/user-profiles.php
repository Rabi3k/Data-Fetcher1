<?php
if(isset($_GET['id']) || isset($_GET['new']))
{
    include "user-profile/user-profile-details.php";
}
else{
    include "user-profile/user-profiles-list.php";
}