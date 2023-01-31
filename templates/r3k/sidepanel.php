<style>
  .sidebar {
    height: 100%;
    width: 0;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 60px;
    box-shadow: inset 0 3px 6px rgba(0,0,0,0.16), 0 4px 6px rgba(0,0,0,0.45);
  }

  .sidebar a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    font-size: 25px;
    color: #818181;
    display: block;
    transition: 0.3s;
  }

  .sidebar a:hover {
    color: #f1f1f1;
  }

  .sidebar .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
  }

  .openbtn {
    font-size: 20px;
    cursor: pointer;
    background-color: #111;
    color: white;
    padding: 10px 15px;
    border: none;
  }

  .openbtn:hover {
    background-color: #444;
  }

  #main {
    transition: margin-left .5s;
    /* padding: 10px 0px 10px 0px; */
  }

  /* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
  @media screen and (max-height: 450px) {
    .sidebar {
      padding-top: 15px;
    }

    .sidebar a {
      font-size: 18px;
    }
  }
</style>
<?php 
$user = $userLogin->GetUser();
$screentypeText = $user->GetScreenTypeText();

?>
<div id="mySidebar" class="sidebar opacity-min">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
  <center class="bg-primary opacity-70 text-center text-light">
   <?php echo $screentypeText ?> 
</center>
  <section class="branches">
    <?php $allBranches = $userLogin->GetUser()->UserBranches();
    if (count($allBranches) > 1) {
    ?>
      <div class="section-title text-center p-2">
        <span class="h3 text-light p-2">Branches</span>

      </div>
      <ul class="nav nav-pills text-light">
        <?php foreach ($allBranches as $key => $value) { ?>

          <li class="nav-item btn-branch btn btn-outline-light m-2" data-toggle="button" aria-pressed="true" tag="<?php echo $value->reference_id ?>">
            <?php echo "$value->city, $value->address"; ?>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>
  </section>
  <hr />
  <section class="Menu">
    <ul class="nav">
      <?php echo $user->isSuperAdmin ? '<li class="nav-item"><a href="/admin">Admin</a></li>':"" ;
       echo $user->isAdmin ? '<li class="nav-item"><a href="/dash">Dashboard</a></li>':""; ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $rootpath ?>/logout.php">logout</a>
      </li>

    </ul>
  </section>

</div>

<script>
  var navStatus = false;

  function toogleNav() {
    if (navStatus === false) {
      openNav();
    } else {
      closeNav();
    }
  }

  function openNav() {
    document.getElementById("mySidebar").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
    navStatus = true;

  }

  function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
    navStatus = false;

  }
</script>