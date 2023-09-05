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

    $CatsIds = array_column($gfMenuObj->categories, 'id');
    $aCats = $integrationGateway->GetBatchTypeByIntegrationAndGfId($CatsIds, $integration->Id, "category");
    $aCatse = filterArrayByKeys($gfMenuObj->categories, ['id', 'name']);
    foreach ($aCatse as  $value) {
        if (array_key_exists($value['id'], $aCats)) {
            $aCats[$value['id']]->name = $value['name'];
        } else {
            $aCats[$value['id']] = (object)array(
                "gf_id" => $value['id'],
                "integration_id" => $integration->Id,
                "type" => "category",
                "gf_menu_id" => $gfMenu->restaurant_id,
                "loyverse_id" => null,
                "name" => $value['name']
            );
        }
        # code...
    }
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
        $cni = array();

        foreach ($aCats as $key => $cat) {
            $Catsi = new stdClass();
            $cat->loyverseId = PostCategory($cat);
            $cni[] = array(
                "categoryName" => $cat->name,
                "loyverse_id" => $cat->loyverseId,
                "gf_id" => $cat->gf_id,
            );
        }
        $aCats = $integrationGateway->InsertOrUpdateBatchPostedType("category", $integration->Id, $gfMenu->menu_id, $cni);
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
        //$result[$key]["loyverse_id"] = $integrationGateway->GetTypeByIntegrationAndGfId($val['id'], $integration->Id,"category")->loyverse_id;
    }
    return $result;
}


function PostCategory($Cats)
{
    global $gfMenu, $integrationGateway, $integration;
    $curl = curl_init();
    $postFieldId = isset($Cats->loyverse_id) && $Cats->loyverse_id != null ? '"id": "' . $Cats->loyverse_id . '", ' : "";
    echo $$postFieldId . "<br/>";
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.loyverse.com/v1.0/categories',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{' . $postFieldId . ' "name": "' . $Cats->name . '","color": "GREY"}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            "Authorization: Bearer $integration->LoyverseToken"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //  echo $Cats->loyverse_id." => $response";

    //return json_decode($response)->id;

    //$integrationGateway->InsertOrUpdatePostedType($Cats["name"], $Cats["id"], "category" ,$integration->Id, $gfMenu->menu_id, json_decode($response)->id);
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
            <input type="hidden" id="hdfIntegrationId" value="<?php echo $id ?>" />
            <h3>Integration with POS Systems</h3>
        </div>
    </center>
    <hr />
    <div class="row justify-content-center align-items-center g-2 mb-2">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">restaurant UID</span>
                <input type="text" name="uid" id="txtUid" class="form-control" placeholder="placeholder" value="<?php echo $integration->gfUid ?>">
            </div>
        </div>
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text">Loyverese Token</span>
                <input type="text" name="uid" id="txtLid" class="form-control" placeholder="placeholder" value="<?php echo $integration->LoyverseToken ?>">
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
                <span class="form-control fs-5 " aria-describedby="prefixrestaurantId" id="txtGfMenuId"><?php echo $gfMenu->menu_id ?></span>
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
                <label for="txtMenu" class="form-label">Menu Details</label>
                <textarea class="form-control" name="" id="txtMenu" rows="3"><?php echo $gfMenu->menu ?></textarea>
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
            <!-- <form method="post"> -->
            <div class="row justify-content-center align-items-center g-2 ">
                <div class="col d-grid gap-2 mx-auto">
                    <span class="btn btn-success fs-4" name="postCategories" id="btnPostCategories">Post Categories</span>
                </div>
                <div class="col d-grid gap-2 mx-auto">
                    <input type="submit" class="btn btn-success fs-4" name="postModifiers" value="Post Modifiers" />
                </div>
                <div class="col d-grid gap-2 mx-auto">
                    <input type="submit" class="btn btn-success fs-4" name="postItems" value="Post Items" />
                </div>

            </div>
            <input type="hidden" name="action" value="submit" />
            <!-- </form> -->
        </div>
        <div class="col-4 ">
            <center class="fs-4">Categories</center>
            <ul class="categories list-group list-group-numbered overflow-auto max-list-5">
                <?php foreach ($aCats as $key => $value) { ?>
                    <li class='category list-group-item form-control <?php echo isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="c-<?php echo $value->gf_id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                        <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                        <?php echo $value->name ?>

                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-4 ">
            <center class="fs-4">Modifiers</center>
            <ul class="list-group list-group-numbered overflow-auto max-list-5">
                <?php foreach ($modifiers as $key => $value) { ?>
                    <li class='list-group-item form-control <?php echo isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="m-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">

                        <?php echo $value->name ?>
                        <span class="float-end fs-6 text-dark">
                            <?php echo $value->optionsNames ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-4 ">
            <center class="fs-4">items</center>
            <ul class="list-group list-group-numbered overflow-auto max-list-5">
                <?php foreach ($fItems as $key => $value) { ?>
                    <li class='list-group-item form-control <?php echo isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="i-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                        <?php echo $value->name ?><span class="float-end fs-6 text-dark"><?php echo $value->sizesNames ?></span></li>
                <?php } ?>
            </ul>
        </div>
    </div>

</div>
<script type="text/javascript">
    $("#btnPostCategories").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("li.category").each(function(key, value) {
            $(this).find(".spinner").toggleClass("visually-hidden");
            $(this).removeClass("is-valid is-invalid");
            let gfid = $(value).attr("id").substring(2);
            let lid = $(value).attr("lid");
            let name = $(value).attr("name");
            let data = JSON.stringify({
                "integration_id": integrationId,
                "gf_menu_id": gfMenuId,
                "gf_id": gfid,
                "l_id": lid,
                "name": name,
            });
            //console.log(data);
            PostCategory(data, this);
        })
    });

    function PostCategory(data, elem) {
        /*
        integration_id =>hdfIntegrationId
        gf_id
        l_id
        name
        gf_menu_id =>txtGfMenuId
         */
        var settings = {
            "url": "/sessionservices/integration.php?q=postcategory",
            "method": "POST",
            "timeout": 0,
            "async": true,
            "headers": {
                "Content-Type": "application/json",
            },
            "data": data,
            "success": function(response) {
                console.log(response);
                $(elem).find(".spinner").toggleClass("visually-hidden");
                $(elem).addClass("is-valid");
            }
        };
        $.ajax(settings).fail(function(response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $(elem).addClass("is-invalid");
        });
    }
</script>