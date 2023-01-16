    <script type='text/javascript'>
        var interval = setInterval(function() {
            myFunction();
        }, 5 * 1000);
    </script>
    <?php
    
    $screentype = $userLogin->GetUser()->GetScreenType();
    //echo "<div> <span>ABCD ==> $screentype</span></div>";
    include "$templatePath/$screentype/load-cards.php";
    ?>