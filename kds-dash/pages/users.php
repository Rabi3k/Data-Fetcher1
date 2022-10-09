<?php
$BranchesId = ($userLogin->GetUser())->UserBranchesId();
$users = $userLogin->GetAllBranchesUsers($BranchesId);
$userid = array_column($users,'id');
if(isset($_GET['id']) && in_array(intval($_GET['id']),$userid))
{
    include "user/user-details.php";
}
else if(isset($_GET['new']))
{
    include "user/user-details.php";
}
else
{
    include "user/users-list.php";

}