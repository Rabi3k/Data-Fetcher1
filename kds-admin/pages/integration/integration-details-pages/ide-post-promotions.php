<!-- Integration Extras Promotions Section-->
<?php

use Src\Controller\IntegrationController;

$promotions = IntegrationController::GetPromotion($integration->gfUid, $integration->Id);

?>
<div class="row">
    <div class="col d-grid gap-2 mx-auto">
        <span class="btn btn-success fs-4" name="postPromotions" id="btnPostPromotions">Post Promotions</span>
    </div>
</div>
<div class="row">
    <div class="table-responsive-sm">
        <table id="tblPromotions" class="table table-striped  w-100">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Outcomes</th>
                    <th>Outcomes function</th>
                    <th>Outcomes discount</th>
                    <th>Udated at</th>
                    <th>Loyverse Id</th>
                    <th></th>
                </tr>
            <tbody class="promotion">
            </tbody>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    <?php include "js/promotions.min.js" ?>
</script>
<!--END Integration Extras Promotions Section-->