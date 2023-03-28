
<div class="row justify-content-center ">
    <ul class="<?php

use Src\TableGateways\UserGateway;

 echo isset($navClass)?$navClass:"nav" ?>">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $rootpath ?>/">Home</a>
        </li>
       
        <?php if (!$userGateway->checkLogin()) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $rootpath ?>/login.php">login</a>
            </li>
        <?php } else { ?>
            <?php if (UserGateway::$user->isSuperAdmin) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="/admin">Super Dash</a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $rootpath ?>/logout.php">logout</a>
            </li>
        <?php } ?>
    </ul>
</div>