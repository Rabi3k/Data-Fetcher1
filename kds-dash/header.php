<?php
?>
<header class="navbar navbar-dark sticky-top bg-secondary flex-md-nowrap p-0 sticky-xl-top">
  <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="/">
    <img class="mb-1" src="/media/System/logo.svg" alt="" width="50" height="30">
    <span>Relax!</span>
  </a>
  <span class="text-light h4"><b>Admin Dashboard</b></span>
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
      <a class="dropdown-item" href="/admin/users?id=<?php echo $loggedUser->id  ?>">my Profile</a>
      <a class="dropdown-item" href="#">Something else here</a>
    </div>
  </div>
</header>

<script src="//code.tidio.co/cf02wzadzvhfykn8u60bxpjnuw5niber.js" async></script>