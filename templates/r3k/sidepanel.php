<style>

</style>
<?php

use Src\TableGateways\UserGateway;

$user = UserGateway::$user;
$screentypeText = $user->GetScreenTypeText();

?>
<div id="mySidebar" class="sidebar opacity-min">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
  <center class="bg-primary opacity-70 text-center text-light">
    <?php echo $user->full_name ?>
  </center>
  <center class="bg-primary opacity-70 text-center text-light">
    <?php echo $screentypeText ?>
  </center>
  <section class="branches">
    <?php $allBranches = $user->UserBranches();
    if (count($allBranches) > 1) {
    ?>
      <div class="section-title text-center p-2">
        <span class="h3 text-light p-2">Branches</span>
      </div>
      <ul class="nav nav-pills text-light">
        <?php
        foreach ($allBranches as $key => $value) { ?>
          <li class="nav-item btn-branch btn btn-outline-light m-2 w-100 active" data-bs-toggle="button" tag="<?php echo $value["gf_refid"] ?>">
            <?php echo $value['alias']; ?>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>
  </section>
  <hr />
  <section class="Menu">
    <ul class="nav">
      <?php echo $user->isSuperAdmin ? '<li class="nav-item"><a href="/admin">Admin</a></li>' : "";
      echo $user->IsAdmin ? '<li class="nav-item"><a href="/dash">Dashboard</a></li>' : ""; ?>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $rootpath ?>/logout.php">logout</a>
      </li>

    </ul>
  </section>
  <hr />
  <section class="buttons">
    <ul class="nav justify-content-start">
      <li class="nav-item">
        <a role="button" class="nav-link btn btn-fullscreen text-start p-0" id="btn-fullscreen" isFull="false">
          Full Screen
        </a>
      </li>
      <li class="nav-item">
        <!-- Modal trigger button -->
        <a role="button" class="nav-link btn btn-md text-start p-0" data-bs-toggle="modal" data-bs-target="#order-history">
          History Orders
        </a>
      </li>
    </ul>
  </section>
</div>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade " id="order-history" tabindex="1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg w-100" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitleId">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body ">

        <div class="table-responsive">
          <table id="tbl_order-history" class="w-100 table table-striped
            table-hover	
            table-borderless
            table-secondary
            align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Due Time</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="table-group-divider">

            </tbody>
            <tfoot>

            </tfoot>
          </table>
        </div>
        <script>
          let table = new DataTable("#tbl_order-history", {
            ajax: '/api/orders/?history=1&userRefIds=' + userRefIds,
            columns: [{
                data: 'id'
              },
              {
                data: 'fulfill_at',
                render: function(data, type, row, meta) {
                  return moment.utc(data).local().format('HH:mm:ss');
                }
              },
              {
                data: null,
                render: function(data, type, row) {
                  return '<button class="btn btn-success btn-redo" oid="' + data["id"] + '">ReDo</button>';
                }
              }
            ]
          });
          $(document).ready(function() {

            $("#order-history").on('shown.bs.modal', function() {
              table.ajax.reload();
            });
            $(document).on("click", ".btn-redo", function() {
              var settings = {
                "url": "/api/orders/",
                "method": "PUT",
                "timeout": 0,
                "headers": {
                  "Content-Type": "application/json"
                },
                "data": JSON.stringify({
                  "orderId": $(this).attr("oid"),
                  "status": false
                }),
              };

              $.ajax(settings).done(function(response) {
                console.log(response);
                table.ajax.reload();
              });
            });
          });
        </script>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- Optional: Place to the bottom of scripts -->