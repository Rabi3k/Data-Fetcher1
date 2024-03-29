<div class="row">
    <div class="col-4">
        <div class="form-group">
            <label for="min">Minimum date:</label>
            <input type="text" id="min" name="min">
        </div>

    </div>
    <div class="col-4">
        <div class="form-group">
            <label for="max">Maximum date:</label>
            <input type="text" id="max" name="min">
        </div>
    </div>
    <div class="col-4 d-flex justify-content-end">
        <button name="reload-btn" id="reload-btn" class="btn btn-primary" role="button">
            <i class="fs-2 bi bi-arrow-repeat ">
            </i>
            <div class="spinner spinner-border text-light visually-hidden" role="status">

            </div>
        </button>
    </div>
</div>
<hr />
<div class="row">
    <div class="table-responsive px-2">
        <table class="table table-bordered table-striped table-hover" id="tblLogs">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Detail</th>
                    <th>User name</th>
                    <th>Error Type</th>
                    <th>Level</th>
                    <th>Datetime</th>
                </tr>
            </thead>

        </table>
    </div>

    <script>
        <?php
        // $liArr = array();

        // foreach ($lines as $lneNum => $line) {
        //     $val = json_decode($line);
        //     $val->lineNum = isset($lneNum)?$lneNum:0;
        //     $val->errorType = isset($val->context->exception->level) ? FriendlyErrorType($val->context->exception->level) : "E_ERROR";

        //     if(empty($val->message) && isset($val->context->exception->error))
        //     {
        //         $val->message = $val->context->exception->error;
        //     }
        //     else if(empty($val->message) && isset($val->context->exception->message))
        //     {
        //         $val->message = $val->context->exception->message;
        //     }

        //     array_push($liArr, $val);
        // }
        ?>
        // var jstr = '<?php //echo addslashes(json_encode($liArr)) 
                        ?>';
        // var jsonfile = JSON.parse(jstr);


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

        $(document).ready(function() {
            // Create date inputs
            minDate = new DateTime($('#min'), {
                format: 'MMMM Do YYYY'
            });
            maxDate = new DateTime($('#max'), {
                format: 'MMMM Do YYYY'
            });

            // DataTables initialisation
            var table = $('#tblLogs').DataTable({
                // dom: 'Blfrtip',
                // buttons: [{
                //     text: 'reload',
                //     action: function(e, dt, node, config) {
                //         reload();
                //     }
                // }],
                ajax: {
                    url: "/api/logs",
                    dataType: 'json',
                    type: 'GET',
                },
                //data: jsonfile,
                columns: [{
                        data: 'lineNum'
                    },
                    {
                        data: 'message'
                    },
                    {
                        data: 'context.User.Name'
                    },
                    {
                        data: 'errorType'
                    },
                    {
                        data: 'level_name'
                    },
                    {
                        data: 'datetime'
                    },
                ],
                columnDefs: [{
                    targets: 5,
                    render: $.fn.dataTable.render.moment('YYYY-MM-DDTHH:mm:ss.SSSSZ', 'YYYY-MM-DD h:mm:ss a')
                }],
                order: [
                    [0, 'desc']
                ],
                "createdRow": function(row, data, dataIndex) {
                    $(row).addClass('clickable-row');
                    $(row).attr('data-href', "?id=" + dataIndex);
                }
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
                $("#reload-btn>.spinner").removeClass("visually-hidden");
                $("#reload-btn>i.bi").addClass("visually-hidden");
                table.ajax.reload(function(){
                    $("#reload-btn>.spinner").addClass("visually-hidden");
                    $("#reload-btn>i.bi").removeClass("visually-hidden");
                });
            })

        });
    </script>
</div>