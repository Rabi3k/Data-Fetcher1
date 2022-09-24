<?php
if(isset($_GET['id']) || isset($_GET['new']))
{
    include "restaurant/restaurant-details.php";
}
else{
    include "restaurant/restaurants-list.php";
}