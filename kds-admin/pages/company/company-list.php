<?php

use Src\TableGateways\CompanyGateway;

$companies = (new CompanyGateway($dbConnection))->GetAll();
?>
    <div class="row">
        <div class="col-4"></div>
        <div class="col-4"><span class="h2">Companies List</span></div>
        <div class="col-4">
            <a class="btn btn-primary float-right" href="?new=" role="button"><i data-feather='plus-circle'></i><span> Ny</span></a>
        </div>
    </div>
<hr/>
<div class="table-responsive ">
    <table class="table table-bordered table-hover" id="tblCompanies">
        <caption>List of users profiles</caption>
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company) {
                
            ?>
                <tr class='clickable-row' data-href="?id=<?php echo $company->id ?>">
                    <th scope="row"><?php echo $company->id ?></th>
                    <td scope="row"><?php echo $company->name ?></td>
                    <td scope="row"><?php echo $company->cvr_nr ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#tblCompanies').DataTable({
            responsive: true,
        });
    });
    $('#tblCompanies tbody').on('click', 'tr', function() {
        //$(this).toggleClass('selected');
        if($(this).data("href"))
        {window.location = $(this).data("href");}
    });
    
</script>