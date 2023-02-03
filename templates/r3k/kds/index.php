    <?php
    
    $screentype = $userLogin->GetUser()->GetScreenType();

    //echo "<div> <span>ABCD ==> $screentype</span></div>";
    include "$templatePath/$screentype/index.php";
    ?>