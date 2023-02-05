
<?php
include "load-cards.php";
include "bottom-bar.php";
?>
<script type='text/javascript'>
    $(function() {
        updateOrderTypeBtn();
        var interval = setInterval(function() {
            GetNewOrder();
            updateOrderTypeBtn();
        }, 5 * 1000);
    });
</script>