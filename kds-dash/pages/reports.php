<?php

use Src\TableGateways\UserGateway;

?>

<script src=""></script>
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
      <table class="table table-borderless table-striped table-hover" id="order-tbl">
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

        </tbody>
        <tfoot class="border border-0">
          <tr>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
        </tfoot>
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
    $.getJSON("/api/orders/" + moment.utc(minDate).local().format("DDMMYYYY") + "-" + moment.utc(maxDate).local().format("DDMMYYYY") + "?all&byDay&userRefIds=" + userRefIds, function(json) {
      jsonfile = json.data;
      //console.log(jsonfile);
    }).then(x => {
      var labels = jsonfile.map(
        function(e) {
          //console.log(e);
          return e.Date;
        });
      //console.log(labels);
      var chartdata1 = jsonfile.map(function(e) {
        return e.orders;
      });
      var chartdata2 = jsonfile.map(function(e) {
        return e.total;
      });
      let data = {
        labels: labels,
        datasets: [{
            label: 'Orders count',
            type: 'line',
            data: chartdata1,
            backgroundColor: [
              'rgba(0, 0, 255, 0.45)'
            ],
            borderColor: [
              'rgb(0, 0, 200)'
            ],
            borderWidth: 1
          },
          {
            label: 'Orders amount',
            data: chartdata2,
            backgroundColor: [
              'rgba(0, 255, 0, 0.45)'
            ],
            borderColor: [
              'rgb(0, 200,)'
            ],
            borderWidth: 1
          }
        ]
      };
      config = {
        responsive: true,
        maintainAspectRatio: false,
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
      dom: '<"container-fluid"<"row"<"col"l><"col align-middle"B><"col"f>>>rtip', //'Bfrtip',
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ],
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
            return moment.utc(data).local().format('HH:mm:ss');
          }
        },
      ],
      drawCallback: function() {
        this.api()
          .columns(3)
          .every(function() {
            let column = this;

            // Create select element
            let select = document.createElement('select');
            let span = document.createElement('div');
            span.classList="w-100 text-center";
            select.classList="w-100";
            span.append("Payment Type    ");
            select.add(new Option(''));
            select.add(new Option('all'));
            column.footer().replaceChildren(span);
            column.footer().append(select);

            // Apply listener for user change in value
            select.addEventListener('change', function() {
              var val = DataTable.util.escapeRegex(select.value);
              if (val === "all") {
                column
                  .search(val ? '.*' : '', true, false)
                  .draw();
              } else {
                column
                  .search(val ? '^' + val + '$' : '', true, false)
                  .draw();
              }
            });

            // Add list of options
            column
              .data()
              .unique()
              .sort()
              .each(function(d, j) {
                if (d) {
                  select.add(new Option(d));
                }
              });
          });
          this.api()
          .columns(2)
          .every(function() {
            let column = this;

            // Create select element
            let select = document.createElement('select');
            let span = document.createElement('div');
            span.classList="w-100 text-center";
            select.classList="w-100";
            span.append("Type    ");
            select.add(new Option(''));
            select.add(new Option('all'));
            column.footer().replaceChildren(span);
            column.footer().append(select);

            // Apply listener for user change in value
            select.addEventListener('change', function() {
              var val = DataTable.util.escapeRegex(select.value);
              if (val === "all") {
                column
                  .search(val ? '.*' : '', true, false)
                  .draw();
              } else {
                column
                  .search(val ? '^' + val + '$' : '', true, false)
                  .draw();
              }
            });

            // Add list of options
            column
              .data()
              .unique()
              .sort()
              .each(function(d, j) {
                if (d) {
                  select.add(new Option(d));
                }
              });
          })
      },
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