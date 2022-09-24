
<div class="row justify-content-center ">
    <ul class="<?php echo isset($navClass)?$navClass:"nav" ?>">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $rootpath ?>/">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="https://funneat.dk/" target="_blank">Fun'N Eat</a>
        </li>
        <li class="nav-item">
            <b><a class="nav-link" href="<?php echo $rootpath ?>/kds">KDS System</a></b>
        </li>
        <?php if (!$userLogin->checkLogin()) { ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $rootpath ?>/login.php">login</a>
            </li>
        <?php } else { ?>
            <?php if ($userLogin->GetUser()->isSuperAdmin) { ?>
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