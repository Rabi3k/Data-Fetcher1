<?php

use Src\TableGateways\RestaurantsGateway;

if (isset($companyId)) {
    $restaurants = (new RestaurantsGateway($dbConnection))->FindByCompanyId($companyId);
} else {
    $restaurants = (new RestaurantsGateway($dbConnection))->GetAll();
}
?>
<div class="row">
    <div class="col-4"></div>
    <div class="col-4"></div>
    <div class="col-4">
        <a class="btn btn-primary float-end <?php echo (!isset($companyId) || empty($companyId)) ? "btn-disabled" : ""; ?>" href="/admin/restaurants/?new=&cid=<?php echo (!isset($companyId) || empty($companyId)) ? "" : $companyId; ?>" role="button">
            <i data-feather='plus-circle'></i>
            <span>Ny</span>
        </a>
    </div>
</div>
<hr />
<div class="table-responsive ">
    <table class="table table-bordered table-hover" id="tblRestaurants">
        <caption>List of users profiles</caption>
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">CVR #</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($restaurants as $restaurant) {

            ?>
                <tr class='clickable-row' data-href="/admin/restaurants/?id=<?php echo $restaurant->id ?>">
                    <th scope="row"><?php echo $restaurant->id ?></th>
                    <td scope="row"><?php echo $restaurant->name ?></td>
                    <td scope="row"><?php echo $restaurant->p_nr ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#tblRestaurants').DataTable({
            responsive: true,
        });
    });
    $('#tblRestaurants tbody').on('click', 'tr', function() {
        //$(this).toggleClass('selected');
        window.location = $(this).data("href");
    });
</script>