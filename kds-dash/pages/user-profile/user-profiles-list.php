<?php

use Src\TableGateways\UserProfilesGateway;

$profiles = (new UserProfilesGateway($dbConnection))->GetAllProfiles();
?>
    <div class="row">
        <div class="col-4"></div>
        <div class="col-4"></div>
        <div class="col-4">
            <a class="btn btn-primary float-right" href="?new=" role="button"><i data-feather='plus-circle'></i><span> Ny</span></a>
        </div>
    </div>
<hr/>
<div class="table-responsive ">
    <table class="table table-bordered table-hover" id="tblUserProfiles">
        <caption>List of users profiles</caption>
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($profiles as $profile) {
                $profiletype = $profile->GetProfileType();
            ?>
                <tr class='clickable-row' data-href="?id=<?php echo $profile->id ?>">
                    <th scope="row"><?php echo $profile->id ?></th>
                    <td scope="row"><?php echo $profile->name ?></td>
                    <td scope="row"><?php echo $profiletype ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#tblUserProfiles').DataTable({
            responsive: true,
        });
    });
    $('#tblUserProfiles tbody').on('click', 'tr', function() {
        //$(this).toggleClass('selected');
        window.location = $(this).data("href");
    });
    
</script>