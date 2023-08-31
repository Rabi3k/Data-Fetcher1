<?php
require "../bootstrap.php";

use Src\TableGateways\OrdersGateway;
use Src\Classes\Order;

$getWholeUrl = $_SERVER['HTTP_HOST'] . "" . $_SERVER['REQUEST_URI'] . "";
if (!$userGateway->checkLogin()) {
        header("Location: /login.php?returnurl=" . $_SERVER['REQUEST_URI']);
        exit();
}
$loggedUser = $userGateway::$user;
if (!$loggedUser->IsAdmin && !$loggedUser->isSuperAdmin) {
        header("Location: /");
        exit();
}
$userid = $loggedUser->id; 


$orderstGateway = new OrdersGateway($dbConnection);

$page = "dashboard";
if (isset($_GET["page"]) && !empty($_GET["page"]) && file_exists("pages/" . $_GET["page"] . ".php")) {

        $page = strtolower($_GET["page"]);
}
$pattern = "/-/i";
$pag= preg_replace($pattern, " ", $page);
$pageTitle = ucfirst($pag);

?>
<!DOCTYPE html>
<html lang="en">
<?php
include "head.php"; ?>

<body>
        <?php include "header.php"; ?>
        <div class="container-fluid">
                <div class="row">
                        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                                <?php include "sidebar.php"; ?>
                        </nav>
                        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
                                <?php include "pages/$page.php"; ?>
                        </main>
                </div>
        </div>
        <?php include "scripts-loader.php" ?>

</body>
<footer class=" bg-dark bg-light text-light">
        <?php

        //include "footer.php";
        ?>
</footer>

</html>