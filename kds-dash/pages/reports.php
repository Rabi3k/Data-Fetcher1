<?php

use Src\TableGateways\UserGateway;

?>
<div class="main">
  <center>
    <h3><b>Online Orders</b></h3>
  </center>
  <hr />

  <div class="container-fluid">
    <div class="row">
      <div class="col d-flex  justify-content-start">
        <div class="form-group">
          <label for="min">Minimum date:</label>
          <input type="text" id="min" name="min">
        </div>

      </div>
      <div class="col d-flex  justify-content-center">
        <div class="form-group">
          <label for="max">Maximum date:</label>
          <input type="text" id="max" name="min">
        </div>
      </div>
      <div class="col d-flex justify-content-end">
        <button name="reload-btn" id="reload-btn" class="btn btn-primary" role="button"><i class="fa fa-refresh" aria-hidden="true"></i></button>
      </div>
    </div>
    <hr />
    <div class="row">
      <table class="table table-bordered table-striped table-hover" id="order-tbl">
        <thead class=" table-dark">
          <tr>
            <th scope="col">Id</th>
            <th scope="col">Client</th>
            <th scope="col">Type</th>
            <th scope="col">Payment Type</th>
            <th scope="col">moms</th>
            <th scope="col">Total</th>
            <th scope="col">Date</th>
          </tr>
        </thead>
        <tbody class=" table-light">
          <tr class="">
            <td scope="row">R1C1</td>
            <td>R1C2</td>
            <td>R1C3</td>
          </tr>
          <tr class="">
            <td scope="row">Item</td>
            <td>Item</td>
            <td>Item</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
  var userRefIds = JSON.parse('<?php echo isset(UserGateway::$user) ? json_encode(UserGateway::$user->Restaurants_Id) : "[]" ?>');
  var minDate, maxDate;

  // Custom filtering function which will search data in column four between two values
  $.fn.dataTable.ext.search.push(
    function(settings, data, dataIndex) {
      var min = minDate.val();
      var max = maxDate.val();
      var date = new Date(data[5]);

      if (
        (min === null && max === null) ||
        (min === null && date <= max) ||
        (min <= date && max === null) ||
        (min <= date && date <= max)
      ) {
        return true;
      }
      return false;
    }
  );
  
//$("#min").datepicker();
  
  $(document).ready(function() {
    // Create date inputs
    minDate = new DateTime($('#min'), {
      format: 'MMMM DD YYYY'
    });
    maxDate = new DateTime($('#max'), {
      format: 'MMMM DD YYYY'
    });

    // DataTables initialisation
    let table = $("#order-tbl").DataTable({
      // dom: 'Blfrtip',
      // buttons: [{
      //     text: 'reload',
      //     action: function(e, dt, node, config) {
      //         reload();
      //     }
      // }],
      /*
      <th scope="col">Id</th>
            <th scope="col">Client</th>
            <th scope="col">Type</th>
            <th scope="col">Payment Type</th>
            <th scope="col">moms</th>
            <th scope="col">Total</th>
            <th scope="col">Date</th>
       */
      ajax: {
        url: "/api/orders/"+moment.utc(new Date()).local().format("DDMMYYYY")+"-"+moment.utc(new Date()).local().format("DDMMYYYY")+"?all&userRefIds=" + userRefIds,
        dataType: 'json',
        type: 'GET',
      },
      //data: jsonfile,
      columns: [{
          data: 'id'
        },
        {
          data: null,
          render: function(data, type, row) {
            return  data["client_first_name"] + " "+ data["client_last_name"] ;
          }

        },
        {
          data: 'type'
        },
        {
          data: 'payment'
        },
        {
          data: 'tax_value'
        },
        {
          data: 'total_price'
        },
        {
          data: 'fulfill_at',
          render: function(data, type, row, meta) {
                  return moment.utc(data).local().format('YYYY-MM-DD h:mm:ss a');
                }
        },
      ],
      // columnDefs: [{
      //   targets: 6,
      //   render: $.fn.dataTable.render.moment('YYYY-MM-DDTHH:mm:ss.SSSSZ', 'YYYY-MM-DD h:mm:ss a')
      // }],
      order: [
        [0, 'desc']
      ]
      // "createdRow": function(row, data, dataIndex) {
      //   $(row).addClass('clickable-row');
      //   $(row).attr('data-href', "?id=" + dataIndex);
      // }
    });

    // Refilter the table
    $('#min, #max').on('change', function() {
      table.draw();
    });
    $('#tblLogs tbody').on('click', 'tr.clickable-row', function() {
      //$(this).toggleClass('selected');
      window.location = $(this).data("href");
    });
    $("#reload-btn").click(function() {
      table.ajax.reload();
    })

  });
</script>