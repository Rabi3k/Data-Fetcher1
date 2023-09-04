<?php

use Pinq\Traversable;
use Src\Controller\GeneralController;
use Src\TableGateways\IntegrationGateway;
use Src\TableGateways\RestaurantsGateway;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $integrationGateway = new IntegrationGateway($dbConnection);
    $integration = $integrationGateway->findById($id);
    if ($integration == null) {
        echo " <script> location.href = '/dash/integrations' </script> ";
        exit;
    }
    $restaurant = (new RestaurantsGateway($dbConnection))->FindById($integration->RestaurantId);
    $gfMenu = $integrationGateway->GetGfMenuByRestaurantId($integration->RestaurantId);
    if ($gfMenu == null) {
        $gfMenu = new stdClass();
        $gfMenu->restaurant_id = $restaurant->id;
    }
} else {
    echo " <script> location.href = '/dash/integrations' </script> ";
}

if (isset($_POST["fetchMenu"])) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.restaurantlogin.com/api/restaurant/$integration->gfUid/menu?active=true&pictures=false",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $retval = curl_exec($curl);

    curl_close($curl);
    $gfMenu->menu = $retval;


    $gfMenu = $integrationGateway->InsertOrupdateGfMenu($gfMenu->menu, $gfMenu->restaurant_id);
}

if (isset($gfMenu->menu) && $gfMenu->menu != null) {
    $gfMenuObj = json_decode($gfMenu->menu);

    //$Cats = array_column($gfMenuObj->categories, 'name');
    $aCats = filterArrayByKeys($gfMenuObj->categories, ['id', 'name']);
    $items = array_column($gfMenuObj->categories, 'items');

    $cItems = array();
    foreach ($items as $key => $value) {
        # code...
        foreach ($value as $key2 => $value2) {
            # code...
            $cItems[] = $value2;
        }
    }
    $fItems = array();
    $modifiers = array();
    foreach ($cItems as $key => $value) {
        # code...
        $i = new stdClass();
        $i->id = $value->id;
        $i->name = $value->name;
        $i->sizes = array_column($value->sizes, 'name');
        $i->sizesNames = implode(" / ", $i->sizes);
        $fItems[] = $i;

        foreach ($value->sizes as $s) {
            foreach ($s->groups as $g) {
                $g->optionsa = array_column($g->options, 'name');
                $g->optionsNames = implode(" / ", $g->optionsa);
                if (!in_array($g, $modifiers)) {
                    $modifiers[] = $g;
                }
            }
        }
        foreach ($value->groups as $g) {
            $g->optionsa = array_column($g->options, 'name');
            $g->optionsNames = implode(" / ", $g->optionsa);
            if (!in_array($g, $modifiers)) {
                $modifiers[] = $g;
            }
        }
    }

    //echo json_encode($cItems);
    $itemsNames = array_column($cItems, 'name');
}

if (isset($_POST['action'])) {

    if (isset($_POST['postCategories'])) {
        foreach ($aCats as $key => $cat) {
            PostCategory($cat);
        }
    }
    if (isset($_POST['postItems'])) {
        echo "Posting items";
    }
}
function filterArrayByKeys(array $input, array $column_keys)
{
    global $integration, $integrationGateway;
    $result      = array();
    $column_keys = array_flip($column_keys); // getting keys as values
    foreach ($input as $key => $val) {
        if (!is_array($val)) {
            $val = get_object_vars($val);
        }
        // getting only those key value pairs, which matches $column_keys
        $result[$key] = array_intersect_key($val, $column_keys);
        $result[$key]["loyverse_category_id"] = $integrationGateway->GetCategoryByIntegrationAndCategoryId($val['id'], $integration->Id)->loyverse_category_id;
    }
    return $result;
}


function PostCategory(array $Cats)
{
    global $gfMenu, $integrationGateway, $integration;
    $curl = curl_init();
    $postFieldId = isset($Cats['loyverse_category_id']) && $Cats['loyverse_category_id'] != null ? '"id": "' . $Cats['loyverse_category_id'] . '", ' : "";
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.loyverse.com/v1.0/categories',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{' . $postFieldId . ' "name": "' . $Cats["name"] . '","color": "RED"}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $Cats['loyverse_category_id']." => $response";
    $integrationGateway->InsertOrupdatePostedCategory($Cats["name"], $Cats["id"], $integration->Id, $gfMenu->menu_id, json_decode($response)->id);
}
?>
<style>
    .max-list-5 {
        max-height: 20em;
    }
</style>
<div class="container-fluid">
    <center>
        <div class="row">
            <h3>Integration with POS Systems</h3>
        </div>
    </center>
    <hr />
    <div class="row justify-content-center align-items-center g-2 mb-2">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">restaurant UID</span>
                <input type="text" name="uid" id="txt-Uid" class="form-control" placeholder="placeholder" value="<?php echo $integration->gfUid ?>">
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">Loyverese Token</span>
                <input type="text" name="uid" id="txt-Uid" class="form-control" placeholder="placeholder" value="<?php echo $integration->LoyverseToken ?>">
            </div>
        </div>
    </div>
    <!-- Gloria food Menu -->
    <div class="row justify-content-center align-items-center g-2 mb-2">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text" id="prefixrestaurantId">Restaurnt Id</span>
                <span class="form-control fs-5 " aria-describedby="prefixrestaurantId">
                    <?php echo $gfMenu->restaurant_id ?>
                </span>
            </div>
        </div>
        <div class="col-4">
            <div class="input-group mb-3">
                <span class="input-group-text" id="prefixId">Date Updated</span>
                <span class="form-control fs-5" aria-describedby="prefixId">
                    <!-- <i class="bi bi-exclamation-diamond"></i> -->
                    <?php echo $gfMenu->update_date ?>

                </span>
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text" id="prefixrestaurantId">menu Id</span>
                <span class="form-control fs-5 " aria-describedby="prefixrestaurantId">
                    <?php echo $gfMenu->menu_id ?>
                </span>
            </div>
        </div>
        <div class="col-2">
            <form method="post">
                <input type="hidden" name="fetchMenu" value="submit" />
                <input type="submit" class="btn btn-primary float-end fs-4" name="fetchMenu" value="Fetch Menu" />
            </form>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label for="" class="form-label">Menu Details</label>
                <textarea class="form-control" name="" id="" rows="3"><?php echo $gfMenu->menu ?></textarea>
            </div>
        </div>
    </div>

    <!-- Menu Info -->
    <div class="row justify-content-center align-items-center g-2 mb-2">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text" id="prefixrestaurantId">Categories</span>
                <span class="form-control fs-5 " aria-describedby="prefixrestaurantId">
                    <?php echo count($aCats) ?>
                </span>
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text" id="prefixId">Modifiers</span>
                <span class="form-control fs-5" aria-describedby="prefixId">
                    <?php echo count($modifiers) ?>
                </span>
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text" id="prefixId">Items</span>
                <span class="form-control fs-5" aria-describedby="prefixId">
                    <?php echo count($itemsNames) ?>
                </span>
            </div>
        </div>
    </div>
    <!-- END Menu Info -->

    <div class="row justify-content-center g-2 ">
        <div class="col-12">
            <form method="post">
                <div class="row justify-content-center align-items-center g-2 ">
                    <div class="col d-grid gap-2 mx-auto">
                        <input type="submit" class="btn btn-success fs-4" name="postCategories" value="Post Categories" />
                    </div>
                    <div class="col d-grid gap-2 mx-auto">
                        <input type="submit" class="btn btn-success fs-4" name="postModifiers" value="Post Modifiers" />
                    </div>
                    <div class="col d-grid gap-2 mx-auto">
                        <input type="submit" class="btn btn-success fs-4" name="postItems" value="Post Items" />
                    </div>

                </div>
                <input type="hidden" name="action" value="submit" />
            </form>
        </div>
        <div class="col-4 ">
            <center class="fs-4">Categories</center>
            <ul class="list-group list-group-numbered overflow-auto max-list-5">
                <?php foreach ($aCats as $key => $value) { ?>
                    <li class='list-group-item' id="<?php echo $value["id"] ?>" name="<?php echo $value["loyverse_category_id"]  ?>">
                        <?php echo $value["name"] ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-4 ">
            <center class="fs-4">Modifiers</center>
            <ul class="list-group list-group-numbered overflow-auto max-list-5">
                <?php foreach ($modifiers as $key => $value) { ?>
                    <li class='list-group-item'><?php echo $value->name ?><span class="float-end fs-6 text-dark"><?php echo $value->optionsNames ?></span></li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-4 ">
            <center class="fs-4">items</center>
            <ul class="list-group list-group-numbered overflow-auto max-list-5">
                <?php foreach ($fItems as $key => $value) { ?>
                    <li class='list-group-item'><?php echo $value->name ?><span class="float-end fs-6 text-dark"><?php echo $value->sizesNames ?></span></li>
                <?php } ?>
            </ul>
        </div>
    </div>

</div>