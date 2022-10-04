<?php
use Src\Classes\Branch;
/*use Src\TableGateways\RestaurantsGateway;

$restaurants = (new RestaurantsGateway($dbConnection))->GetAllRestaurants();*/
$branches = $lRestaurant->branches;
?>
    <div class="row">
        <div class="col-4"></div>
        <div class="col-4"></div>
        <div class="col-4">
            <a class="btn btn-primary float-right" href="/admin/branches?rid=<?php echo $lRestaurant->id ?>&new" role="button"><i data-feather='plus-circle'></i><span> Ny</span></a>
        </div>
    </div>
<hr/>
<div class="table-responsive ">
    <table class="table table-bordered table-hover" id="tblbranches">
        <caption>List of users profiles</caption>
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">city</th>
                <th scope="col">zip code</th>
                <th scope="col">address</th>
                <th scope="col">country</th>
                <th scope="col">Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($branches as $branch) {
                
            ?>
                <tr class='clickable-row' data-href="/admin/branches?rid=<?php echo $lRestaurant->id ?>&id=<?php echo $branch->id ?>">
                    <th scope="row"><?php echo $branch->id ?></th>
                    <td scope="row"><?php echo $branch->city ?></td>
                    <td scope="row"><?php echo $branch->zip_code ?></td>
                    <td scope="row"><?php echo $branch->address ?></td>
                    <td scope="row"><?php echo $branch->country ?></td>
                    <td scope="row"><?php echo $branch->cvr ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#tblbranches').DataTable({
            responsive: true,
        });
    });
    $('#tblbranches tbody').on('click', 'tr', function() {
        //$(this).toggleClass('selected');
        window.location = $(this).data("href");
    });
    
</script>