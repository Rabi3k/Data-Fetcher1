    <?php
    
    $screentype = $userGateway->GetUser()->GetScreenType();

    //echo "<div> <span>ABCD ==> $screentype</span></div>";
    include "$templatePath/$screentype/index.php";
    ?>