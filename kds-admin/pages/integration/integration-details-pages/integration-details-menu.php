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
                        <textarea class="form-control"  id="txtMenu" rows="6"><?php echo $gfMenu->menu ?></textarea>
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