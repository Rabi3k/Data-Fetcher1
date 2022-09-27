<?php

if (!$userLogin->checkLogin()) {
    header("Location: $rootpath/login.php?returnurl=/kds");
    exit();
}
$PageTitle = "KDS System";
include "../$templatePath/head.php";
?>



<?php
include "../$templatePath/header.php";
?>

<body class="wide">
    <div class="full-div bgimg ">
        <div class="full-div  opacity-min bg-light">
            <script type='text/javascript'>
                var interval = setInterval(function() {
                    myFunction();
                }, 5 * 1000);
            </script>
            <?php
            include "load-cards.php";
            ?>
        </div>
        <?php
        include "../$templatePath/footer.php";
        ?>
</body>

</html>