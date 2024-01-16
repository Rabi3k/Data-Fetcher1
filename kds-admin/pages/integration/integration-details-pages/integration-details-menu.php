<?php
 $gfMenu = $integrationGateway->GetGfMenuByRestaurantId($integration->RestaurantId);
 if ($gfMenu == null) {
     $gfMenu = new stdClass();
     $gfMenu->restaurant_id = $restaurant->id;
 }
if (isset($_POST["fetchMenu"])) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.restaurantlogin.com/api/restaurant/$integration->gfUid/menu?active=true&pictures=true",
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
    $aCats = ($postedElements != null && $postedElements['category'] != null) ? $postedElements['category'] : array();
    $aCatse = filterArrayByKeys($gfMenuObj->categories, ['id', 'name']);
    foreach ($gfMenuObj->categories as  $value) {
        if (array_key_exists($value->id, $aCats)) {
            $aCats[$value->id]->hasIssue = false;

            if ($aCats[$value->id]->name != $value->name) {
                $aCats[$value->id]->name = $value->name;
                $aCats[$value->id]->hasIssue = true;
            }
        } else {
            $aCats[$value->id] = (object)array(
                "gf_id" => $value->id,
                "integration_id" => $integration->Id,
                "type" => "category",
                "gf_menu_id" => $gfMenu->restaurant_id,
                "loyverse_id" => null,
                "name" => $value->name,
                "hasIssue" => false
            );
        }
    }
    /*,'modifier','option','item','variant'
     */
    $pItems = ($postedElements != null && $postedElements['item'] != null) ? $postedElements['item'] : array();
    $pModifier = ($postedElements != null && $postedElements['modifier'] != null) ? $postedElements['modifier'] : array();
    $pOption = ($postedElements != null && $postedElements['option'] != null) ? $postedElements['option'] : array();
    $pVariant = ($postedElements != null && $postedElements['variant'] != null) ? $postedElements['variant'] : array();
    $items = array_column($gfMenuObj->categories, 'items');

    $cItems = array();
    $fItems = array();
    $modifiers = array();
    foreach ($gfMenuObj->categories as $key => $c) {
        AddModifiers($c->groups);
        foreach ($c->items as $key => $i) {
            # code...
            foreach ($c->groups as $g) {
                if (isset($i->groups)) {
                    if (!in_array($g, $i->groups)) {
                        $i->groups[] = $g;
                        //echo json_encode($i->groups);

                    }
                }
            }
            $i->menu_category_name = $c->name;
            $cItems[] = $i;
        }
    }
    foreach ($cItems as $key => $value) {
        //echo json_encode($value->groups);

        # code...
        $i = new stdClass();
        $i->id = $value->id;
        $i->name = $value->name;
        $i->description = $value->description;
        $i->gf_category_id = $value->menu_category_id;
        $i->gf_category_name = $value->menu_category_name;
        $i->sizes = $value->sizes;
        $i->groups = $value->groups;
        $i->price = $value->price;
        $i->sizese = isset($value->sizes) ? array_column($value->sizes, 'name') : array();
        $i->sizesNames = implode(" / ", $i->sizese);
        $i->loyverse_id = isset($pItems[$value->id]) ? $pItems[$value->id]->loyverse_id : null;
        $i->picture_hi_res = $value->picture_hi_res;
        $fItems[$value->id] = $i;

        foreach ($value->sizes as $s) {
            $s->loyverse_id = isset($pVariant[$s->id]) ? $pVariant[$s->id]->loyverse_id : null;
            AddModifiers($s->groups);
        }
        AddModifiers($value->groups);
    }

    //echo json_encode($cItems);
    $itemsNames = array_column($cItems, 'name');
}
function AddModifiers($groups)
{
    global $modifiers, $pOption, $pModifier;
    foreach ($groups as $g) {
        $g->optionsa = array_column($g->options, 'name');
        $g->optionsNames = implode(" / ", $g->optionsa);
        $g->loyverse_id = isset($pModifier[$g->id]) ? $pModifier[$g->id]->loyverse_id : null;
        foreach ($g->options as $o) {
            $o->loyverse_id = isset($pOption[$o->id]) ? $pOption[$o->id]->loyverse_id : null;
        }
        if (!in_array($g, $modifiers)) {
            // echo json_encode($g);
            $modifiers[] = $g;
        }
    }
}

?>
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
            <input type="submit" class="btn btn-info text-light float-end fs-4" name="fetchMenu" value="Fetch Menu" />
        </form>
    </div>
    <div class="col-12">
        <div class="accordion" id="accordion-menu-text">
            <div class="accordion-item ">
                <h2 class="accordion-header text-center bg-info" id="menu-text-tab">
                    <button class="accordion-button collapsed d-block text-center fs-4 text-light bg-info" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-menu-text" aria-expanded="true" aria-controls="collapse-menu-text">
                        Menu Text
                    </button>
                </h2>
                <div id="collapse-menu-text" class="accordion-collapse collapse" aria-labelledby="categories-tab" data-bs-parent="#accordion-menu-text">
                    <div class="accordion-body">
                        <label for="txtMenu" class="form-label">Menu Details</label>
                        <textarea class="form-control" id="txtMenu" rows="6"><?php echo $gfMenu->menu ?></textarea>
                    </div>
                </div>
            </div>

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
<div class="accordion" id="accordion-menu-details">
    <div class="accordion-item">
        <h2 class="accordion-header" id="categories-tab">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-categories" aria-expanded="true" aria-controls="collapse-categories">
                Categories
            </button>
        </h2>
        <div id="collapse-categories" class="accordion-collapse collapse" aria-labelledby="categories-tab" data-bs-parent="#accordion-menu-details">
            <div class="accordion-body">
                <?php include "idm-post-categories.php" ?>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="modifiers-tab">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-modifiers" aria-expanded="true" aria-controls="collapse-modifiers">
                Modifiers
            </button>
        </h2>
        <div id="collapse-modifiers" class="accordion-collapse collapse" aria-labelledby="modifiers-tab" data-bs-parent="#accordion-menu-details">
            <div class="accordion-body">
                <?php include "idm-post-modifiers.php" ?>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="items-tab">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-items" aria-expanded="true" aria-controls="collapse-items">
                Items
            </button>
        </h2>
        <div id="collapse-items" class="accordion-collapse collapse" aria-labelledby="items-tab" data-bs-parent="#accordion-menu-details">
            <div class="accordion-body">
                <?php include "idm-post-items.php" ?>
            </div>
        </div>
    </div>
</div>