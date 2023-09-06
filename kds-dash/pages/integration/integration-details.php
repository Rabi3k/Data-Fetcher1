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

    $postedElements = $integrationGateway->GetBatchTypeByIntegrationAndMenu($gfMenu->menu_id, $integration->Id,);
    $CatsIds = array_column($gfMenuObj->categories, 'id');
    $aCats = $postedElements['category'];
    $aCatse = filterArrayByKeys($gfMenuObj->categories, ['id', 'name']);
    foreach ($aCatse as  $value) {
        if (array_key_exists($value['id'], $aCats)) {
            $aCats[$value['id']]->hasIssue = false;

            if ($aCats[$value['id']]->name != $value['name']) {
                $aCats[$value['id']]->name = $value['name'];
                $aCats[$value['id']]->hasIssue = true;
            }
        } else {
            $aCats[$value['id']] = (object)array(
                "gf_id" => $value['id'],
                "integration_id" => $integration->Id,
                "type" => "category",
                "gf_menu_id" => $gfMenu->restaurant_id,
                "loyverse_id" => null,
                "name" => $value['name'],
                "hasIssue" => false
            );
        }
    }
    /*,'modifier','option','item','variant'
     */
    $pItems = $postedElements['item'];
    $pModifier = $postedElements['modifier'];
    $pOption = $postedElements['option'];
    $pVariant = $postedElements['variant'];
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
        $i->gf_category_id = $value->menu_category_id;
        $i->sizes = $value->sizes;
        $i->groups = $value->groups;
        $i->price = $value->price;
        $i->sizese = array_column($value->sizes, 'name');
        $i->sizesNames = implode(" / ", $i->sizese);
        $i->loyverse_id = isset($pItems[$value->id]) ? $pItems[$value->id] : null;
        $fItems[$value->id] = $i;

        foreach ($value->sizes as $s) {
            $s->loyverse_id = isset($pVariant[$g->id]) ? $pVariant[$g->id]->loyverse_id : null;
            foreach ($s->groups as $g) {
                $g->optionsa = array_column($g->options, 'name');
                $g->optionsNames = implode(" / ", $g->optionsa);
                $g->loyverse_id = isset($pModifier[$g->id]) ? $pModifier[$g->id]->loyverse_id : null;
                if (!in_array($g, $modifiers)) {
                    $modifiers[] = $g;
                }
            }
        }
        foreach ($value->groups as $g) {
            $g->optionsa = array_column($g->options, 'name');
            $g->optionsNames = implode(" / ", $g->optionsa);
            $g->loyverse_id = isset($pModifier[$g->id]) ? $pModifier[$g->id]->loyverse_id : null;
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

    .form-control.has-issue {
        border-color: var(--bs-yellow);
        padding-right: calc(1.5em + 0.75rem);
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23ffc107" class="bi bi-exclamation-triangle" viewBox="0 0 16 16"><path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/><path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/></svg>');
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
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
                    <span class="btn btn-success fs-4" name="postModifiers" id="btnPostModifiers">Post Modifiers</span>
                </div>
                <div class="col d-grid gap-2 mx-auto">
                    <span class="btn btn-success fs-4" name="postItems" id="btnPostItems">Post Items</span>
                </div>

            </div>
            <input type="hidden" name="action" value="submit" />
            <!-- </form> -->
        </div>
        <div class="col-4 ">
            <center class="fs-4">Categories</center>
            <ul class="categories card list-group  overflow-auto max-list-5">
                <?php foreach ($aCats as $key => $value) { ?>
                    <li class='menu category list-group-item form-control <?php echo isset($value->hasIssue) && $value->hasIssue != false ? "has-issue" : (isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid'  : "is-invalid")  ?>' id="c-<?php echo $value->gf_id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                        <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                        <span class="fs-5 fw-bolder"><?php echo $value->name ?></span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-4 ">
            <center class="fs-4">Modifiers</center>
            <ul class="modifiers card list-group  overflow-auto max-list-5">
                <?php foreach ($modifiers as $key => $value) { ?>
                    <li class='menu modifier list-group-item form-control <?php echo isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="m-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>">
                        <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                        <span class="fs-5 fw-bolder"><?php echo $value->name ?></span>
                        <ul class="options card list-group  overflow-auto max-list-5">
                            <?php foreach ($value->options as $o) { ?>
                                <li class='menu option list-group-item form-control <?php echo isset($pOption[$o->id]) && $pOption[$o->id]->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="m-<?php echo $o->id ?>" lid="<?php echo  $pOption[$o->id]->loyverse_id  ?>" name="<?php echo $o->name ?>" price="<?php echo $o->price ?>">
                                    <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                    <span class="fs-6 fw-semibold"><?php echo $o->name ?> </span>
                                    <span class="fs-6 float-end"><?php echo $o->price ?> DKK</span>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-4 ">
            <center class="fs-4">items</center>
            <ul class="items card list-group  overflow-auto max-list-5">
                <?php foreach ($fItems as $key => $value) { ?>
                    <li class='menu item list-group-item form-control <?php echo isset($value->loyverse_id) && $value->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="i-<?php echo $value->id ?>" lid="<?php echo $value->loyverse_id  ?>" name="<?php echo $value->name ?>" price="<?php echo $value->price ?>">
                        <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                        <span class="fs-5 fw-bolder"><?php echo $value->name ?></span>
                        <span class="fs-6 fw-bolder float-end"><?php echo $value->price ?> DKK</span>

                        <ul class="options card list-group  overflow-auto max-list-5">
                            <?php foreach ($value->sizes as $o) { ?>
                                <li class='menu variant list-group-item form-control <?php echo isset($pVariant[$o->id]) && $pVariant[$o->id]->loyverse_id != null ? 'is-valid' : "is-invalid"   ?>' id="m-<?php echo $o->id ?>" lid="<?php echo  $pOption[$o->id]->loyverse_id  ?>" name="<?php echo $o->name ?>" price="<?php echo $o->price ?>">
                                    <span class="spinner spinner-border spinner-border-sm float-end visually-hidden" role="status" aria-hidden="true"></span>
                                    <span class="fs-6 fw-semibold"><?php echo $o->name ?> </span>
                                    <span class="fs-6 float-end"><?php echo $o->price ?> DKK</span>
                                </li>
                            <?php } ?>
                        </ul>

                    <?php } ?>
            </ul>
        </div>
    </div>

</div>
<script type="text/javascript">
    var items =  JSON.parse('<?php echo json_encode( $fItems) ?>');
    $("#btnPostCategories").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("li.category.has-issue,li.category.is-invalid").each(function(key, value) {
            $(this).find(".spinner").toggleClass("visually-hidden");
            $(this).removeClass("has-issue");
            $(this).removeClass("is-invalid");

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
    $("li.menu").on("click",function(){
        if($(this).hasClass("is-valid"))
        {
            $(this).addClass("has-issue");
            $(this).removeClass("is-valid");
        }
    })

    $("#btnPostModifiers").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("li.modifier.has-issue,li.modifier.is-invalid").each(function(key, value) {
            $(this).find(".spinner").toggleClass("visually-hidden");
            $(this).removeClass("has-issue");
            $(this).removeClass("is-invalid");


            let options = [];
            $($(this).find("li.option")).each(function(oKey, oVal) {
                $(this).removeClass("has-issue");
                $(this).removeClass("is-invalid");
                $(this).removeClass("is-valid");
                let gfid = $(oVal).attr("id").substring(2);
                let lid = $(oVal).attr("lid");
                let name = $(oVal).attr("name");
                let price = $(oVal).attr("price");
                options.push({
                    "gf_id": gfid,
                    "l_id": lid,
                    "name": name,
                    "price": parseInt(price)
                })
            })
            let gfid = $(value).attr("id").substring(2);
            let lid = $(value).attr("lid");
            let name = $(value).attr("name");
            let data = JSON.stringify({
                "integration_id": integrationId,
                "gf_menu_id": gfMenuId,
                "gf_id": gfid,
                "l_id": lid,
                "name": name,
                "options": options

            });

            //console.log(data);
            PostModifier(data, this);
        })
    });
    $("#btnPostItems").on("click", function() {
        let integrationId = $(hdfIntegrationId).val();
        let gfMenuId = $(txtGfMenuId).text();
        $("li.item.has-issue,li.item.is-invalid").each(function(key, value) {
            $(this).find(".spinner").toggleClass("visually-hidden");
            $(this).removeClass("has-issue");
            $(this).removeClass("is-invalid");

            let gfid = $(this).attr("id").substring(2);
            $($(this).find("li.variant")).each(function(oKey, oVal) {
                $(this).removeClass("has-issue");
                $(this).removeClass("is-invalid");
                $(this).removeClass("is-valid");

            })
            
            let data = JSON.stringify(items[gfid]);

            //console.log(data);
            PostItem(data, this);
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
            //"async": true,
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
    function PostItem(data, elem) {
        /*
        integration_id =>hdfIntegrationId
        gf_id
        l_id
        name
        gf_menu_id =>txtGfMenuId
         */
        var settings = {
            "url": "/sessionservices/integration.php?q=postitem",
            "method": "POST",
            "timeout": 0,
            //"async": true,
            "headers": {
                "Content-Type": "application/json",
            },
            "data": data,
            "success": function(response) {
                console.log(response);
                $(elem).find(".spinner").toggleClass("visually-hidden");
                $(elem).addClass("is-valid");
                $(elem).find("li.variant").addClass("is-valid");
            }
        };
        $.ajax(settings).fail(function(response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $(elem).addClass("is-invalid");
            $(elem).find("li.variant").addClass("is-invalid");
        });
    }
    function PostModifier(data, elem) {
        /*
        integration_id =>hdfIntegrationId
        gf_id
        l_id
        name
        gf_menu_id =>txtGfMenuId
         */
        var settings = {
            "url": "/sessionservices/integration.php?q=postmodifier",
            "method": "POST",
            "timeout": 0,
            //"async": true,
            "headers": {
                "Content-Type": "application/json",
            },
            "data": data,
            "success": function(response) {
                console.log(response);
                $(elem).find(".spinner").toggleClass("visually-hidden");
                $(elem).addClass("is-valid");
                $(elem).find("li.option").addClass("is-valid");
            }
        };
        $.ajax(settings).fail(function(response) {
            console.log(response);
            $(elem).find(".spinner").toggleClass("visually-hidden");
            $(elem).addClass("is-invalid");
            $(elem).find("li.option").addClass("is-invalid");
        });
    }
</script>