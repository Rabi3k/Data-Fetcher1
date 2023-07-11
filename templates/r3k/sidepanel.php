<style>
  
</style>
<?php

use Src\TableGateways\UserGateway;

$user = UserGateway::$user;
$screentypeText = $user->GetScreenTypeText();

?>
<div id="mySidebar" class="sidebar opacity-min">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
  <center class="bg-primary opacity-70 text-center text-light">
    <?php echo $user->full_name ?>
  </center>
  <center class="bg-primary opacity-70 text-center text-light">
    <?php echo $screentypeText ?>
  </center>
  <section class="branches">
    <?php $allBranches = $user->UserBranches();
    if (count($allBranches) > 1) {
    ?>
      <div class="section-title text-center p-2">
        <span class="h3 text-light p-2">Branches</span>
      </div>
      <ul class="nav nav-pills text-light">
        <?php 
        foreach ($allBranches as $key => $value) { ?>
          <li class="nav-item btn-branch btn btn-outline-light m-2 w-100 active" data-bs-toggle="button"  tag="<?php echo $value["gf_refid"] ?>">
            <?php echo $value['alias']; ?>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>
  </section>
  <hr />
  <section class="Menu">
    <ul class="nav">
      <?php echo $user->isSuperAdmin ? '<li class="nav-item"><a href="/admin">Admin</a></li>' : "";
      echo $user->IsAdmin ? '<li class="nav-item"><a href="/dash">Dashboard</a></li>' : ""; ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $rootpath ?>/logout.php">logout</a>
      </li>

    </ul>
  </section>
  <hr />
  <section class="buttons">
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link btn btn-fullscreen" id="btn-fullscreen" isFull = "false"><i class="bi bi-fullscreen"></i>Full Screen</a>
      </li>

    </ul>
  </section>
</div>