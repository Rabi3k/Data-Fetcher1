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
          <input type="text" id="min" name="min" class="selector">
        </div>

      </div>
      <div class="col d-flex  justify-content-center">
        <div class="form-group">
          <label for="max">Maximum date:</label>
          <input type="text" id="max" name="max" class="selector">
        </div>
      </div>
      <div class="col d-flex justify-content-end">
        <button name="reload-btn" id="reload-btn" class="btn btn-primary" role="button"><i class="fa fa-refresh" aria-hidden="true"></i></button>
      </div>
    </div>
    <hr />
    <div class="row">
      <canvas id="orderChartBars"></canvas>
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
            <th scope="col">Time</th>
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

  $.datepicker.setDefaults($.datepicker.regional["da"]);
  $(".selector").datepicker({
    dateFormat: 'yy-mm-dd',
  });
  $(".selector").datepicker("setDate", new Date());
  let config = {};

  function refreshChart() {
    var jsonfile = [];
    $.getJSON("/api/orders/" + moment.utc(minDate).local().format("DDMMYYYY") + "-" + moment.utc(maxDate).local().format("DDMMYYYY") + "?all&userRefIds=" + userRefIds, function(json) {
      jsonfile = json.data;
      console.log(jsonfile);
    }).then(x => {
      var labels = jsonfile.map(
        function(e) {
          console.log(e);
          return e.id;
        });
      console.log(labels);
      var chartdata = jsonfile.map(function(e) {
        return e.total_price;
      });
      let data = {
        labels: labels,
        datasets: [{
          label: 'Orders',
          data: chartdata,
          backgroundColor: [
            'rgba(0, 0, 255, 0.45)'
          ],
          borderColor: [
            'rgb(0, 0, 200)'
          ],
          borderWidth: 1
        }]
      };
      config = {
        type: 'bar',
        data: data,
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        },
      };
      orderChartBars.destroy();
      orderChartBars = new Chart(ctxBars, config)
    });

  }
  let ctxBars = document.getElementById('orderChartBars').getContext('2d');
  let orderChartBars = new Chart(ctxBars, config);
  refreshChart();
  //[{"id":11052316,"qty":1,s"name":"Pizza Prosciutto"},{"id":11052319,"qty":3,"name":"Coffee"},{"id":11052321,"qty":9,"name":"Lemonade"},{"id":11052318,"qty":3,"name":"Spaghetti Carbonara"}];



  $(document).ready(function() {
    // Create date inputs
    minDate = new Date($('#min').val());
    maxDate = new Date($('#max').val());

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
        url: "/api/orders/" + moment.utc(minDate).local().format("DDMMYYYY") + "-" + moment.utc(maxDate).local().format("DDMMYYYY") + "?all&userRefIds=" + userRefIds,
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
            return data["client_first_name"] + " " + data["client_last_name"];
          }

        },
        {
          data: 'type'
        },
        {
          data: 'payment'
        },
        {
          data: 'tax_value',
          render: function(data, type, row, meta) {
            let formatting_options = {
              style: 'currency',
              currency: row["currency"],
              minimumFractionDigits: 2,
            }
            let currencyString = new Intl.NumberFormat("da-DK", formatting_options);
            return currencyString.format(data);
          }
        },
        {
          data: 'total_price',
          render: function(data, type, row, meta) {
            let formatting_options = {
              style: 'currency',
              currency: row["currency"],
              minimumFractionDigits: 2,
            }
            let currencyString = new Intl.NumberFormat("da-DK", formatting_options);
            return currencyString.format(data);
          }
        },
        {
          data: 'fulfill_at',
          render: function(data, type, row, meta) {
            return moment.utc(data).local().format('YYYY-MM-DD');
          }
        },
        {
          data: 'fulfill_at',
          render: function(data, type, row, meta) {
            return moment.utc(data).local().format('h:mm:ss a');
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

      minDate = new Date($('#min').val());
      maxDate = new Date($('#max').val());
      refreshChart();
      //chart.update();

      table.ajax.url("/api/orders/" + moment.utc(minDate).local().format("DDMMYYYY") + "-" + moment.utc(maxDate).local().format("DDMMYYYY") + "?all&userRefIds=" + userRefIds).load();
      // table.ajax.reload();
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