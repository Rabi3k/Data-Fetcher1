<?php
require "../bootstrap.php";
$getWholeUrl = $_SERVER['HTTP_HOST']."".$_SERVER['REQUEST_URI']."";
if(!$userLogin->checkLogin())
{
    header("Location: $rootpath/login.php");
    exit();
}
$userid=$userLogin->GetUser()->id;
?>
<!DOCTYPE html>
<html lang="en">
<?php 
include "head.php";?>
<body>
        <?php include "header.php"; ?>
        <div class="container-fluid">
                <div class="row">
                        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                                <?php include "sidebar.php"; ?>
                        </nav>
                        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
                                <?php $page="dashboard";
                                if(isset($_GET["page"]) && !empty($_GET["page"]))
                                {
                                        $page=strtolower($_GET["page"]);
                                }
                                include "pages/$page.php"; ?>
                        </main>
                </div>
        </div>
        <?php
                include "scripts-loader.php"
        ?>
</body>
<footer class=" bg-dark bg-light text-light">
<?php

//include "footer.php";
?>
</footer>
</html>