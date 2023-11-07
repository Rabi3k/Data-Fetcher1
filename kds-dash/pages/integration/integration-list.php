<?php

use Src\TableGateways\IntegrationGateway;
$restaurants = explode(",",($userGateway->GetUser())->Restaurants_Id[0]);
$integrations = (new IntegrationGateway($dbConnection))->findAllByRestaurantIds($restaurants);
?>
<div class="container-fluid">
    <div class="row">
        <center class="h3"> Integreations added to you profiles</center>
    </div>
    <hr />
    <div class="row">
        <div class="col-10"></div>
        <div class="col-2 pull-end">

            <a name="New" id="btnNew" class="btn btn-success" href="?new" role="button">
                <i class="bi bi-plus-circle"></i>

                Ny
            </a>
        </div>
    </div>
    <div class="row">
        <?php foreach ($integrations as $key => $value) { ?>
            <div class="col-4 py-1">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $value->RestaurantName?></h4>
                        <p class="card-text">integreation between Pos and delivery system</p>
                        <a href="integrations/<?php echo $value->Id?>" class="btn btn-primary stretched-link"></a>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>
</div>