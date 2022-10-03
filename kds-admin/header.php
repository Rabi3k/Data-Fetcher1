<?php
?>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
  <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="/">
    <img class="mb-1" src="/media/System/logo.svg" alt="" width="50" height="30">
    <span>Relax!</span>
  </a>


  <!-- <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
      <a class="nav-link" href="/logout.php">Sign out</a>
    </li>
  </ul> -->

  <div class="dropdown show">
    <a class="btn btn-lg dropdown-toggle text-light" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <i class="fa fa-user fa-2" aria-hidden="true"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
      <a class="dropdown-item" href="/logout.php">Sign out</a>
      <a class="dropdown-item" href="/admin/users?id=<?php echo $userLogin->GetUser()->id  ?>">my Profile</a>
      <a class="dropdown-item" href="#">Something else here</a>
    </div>
  </div>
</nav>