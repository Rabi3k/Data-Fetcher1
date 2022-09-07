
<script type="text/javascript">
var timestamp = '<?=time();?>';
function updateTime(){
  $('#time').html(Date(timestamp));
  timestamp++;
}
$(function(){
  setInterval(updateTime, 1000);
});
</script>
<!-- Navbar (sticky bottom) -->

<header class="sticky-top">
  
  <div class="w3-bar w3-white w3-center w3-padding w3-opacity-min w3-hover-opacity-off">
  <div class="row">
  <div class="col-12">
    <p id ='time' class="text-center">     
    </p></div></div>
    <a href="<?php echo $rootpath?>/" style="width:25%" class="w3-bar-item w3-button">Home</a>
    <a href="https://funneat.dk/" style="width:25%" class="w3-bar-item w3-button" target="_blank">Fun'N Eat</a>
    <a href="<?php echo $rootpath?>/kds" style="width:25%" class="w3-bar-item w3-button">KDS System</a>
    <?php
    if (!$_SESSION || !isset($_SESSION['loggedin']) || $_SESSION["loggedin"] === false ) 
    {
    ?>
    <a href="<?php echo $rootpath?>/login.php" style="width:25%" class="w3-bar-item w3-button w3-hover-black">login</a>
    <?php
    }
    else
    {
    ?>
    <a href="<?php echo $rootpath?>/logout.php" style="width:25%" class="w3-bar-item w3-button w3-hover-black">logout</a>
    <?php
    }
    ?>
  </div>
</header>