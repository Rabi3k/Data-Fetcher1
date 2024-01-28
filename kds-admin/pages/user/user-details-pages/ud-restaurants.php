<?php

use Src\TableGateways\CompanyGateway;

$allComapnies = (new CompanyGateway($dbConnection))->GetAllAdvanced();
$UserComanyRelation = $compTree;
$UserRestaurantsIds = array();
?>

<form method="post" name="setAccess">
    <?php

    foreach ($UserComanyRelation as $key => $value) {

        $UserCompanyIds = (object)array(
            "companyId" => $value->id,
            "restaurantId" => null
        );
        foreach ($value->restaurants as $rkey => $r) {
            $UserCompanyIds->restaurantId[] = $r->id;
        }
        $UserRestaurantsIds[] = $UserCompanyIds;
    }
   
    ?>
    <div class="row mb-3">
        <div class="col">
            <div class="table-responsive">
                <table class="table table-info w-100" id="tblComanies">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Restaurants</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allComapnies as $key => $c) {

                        ?>
                            <tr>
                                <td></td>
                                <td><?php echo $c->id ?></td>
                                <td><?php echo $c->name ?></td>
                                <td><?php echo $c->restaurants ?></td>
                                <td></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">

            <button type="button" class="btn btn-info float-end" id="btn-save-userRelations">
                <i class="bi bi-save"></i>
                Save
            </button>
        </div>
    </div>

    <!-- <button type="submit" name="set-access" class="btn btn-primary">Save</button> -->
</form>

<script>
    let $UserRestaurantsIds = JSON.parse('<?php echo json_encode($UserRestaurantsIds) ?>');
    <?php include("js/user-restaurants.min.js") ?>
</script>