<div class="accordion" id="accordion-extras">
    <div class="accordion-item">
        <h2 class="accordion-header" id="promotions-tab">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-promotions" aria-expanded="true" aria-controls="collapse-promotions">
                Promotions
            </button>
        </h2>
        <div id="collapse-promotions" class="accordion-collapse collapse" aria-labelledby="promotions-tab" data-bs-parent="#accordion-extras">
            <div class="accordion-body">
                <?php include "ide-post-promotions.php" ?>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="delivery-tab">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-delivery" aria-expanded="true" aria-controls="collapse-delivery">
                Delivery
            </button>
        </h2>
        <div id="collapse-delivery" class="accordion-collapse collapse" aria-labelledby="delivery-tab" data-bs-parent="#accordion-extras">
            <div class="accordion-body">
                <?php include "ide-post-delivery.php" ?>
            </div>
        </div>
    </div>

</div>