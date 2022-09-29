<div class="row">
    <div class="col-4">
        <div class="form-group">
            <label for="min">Minimum date:</label>
            <input type="text" id="min" name="min">
        </div>
        <div class="form-group">
            <label for="max">Maximum date:</label>
            <input type="text" id="max" name="min">
        </div>
    </div>
    <div class="col-4"></div>
    <div class="col-4">
        <a class="btn btn-primary float-right" href="?new=" role="button"><i data-feather='plus-circle'></i><span> Ny</span></a>
    </div>
</div>
<hr />
<div class="row">
    <div class="table-responsive px-2">
        <table class="table table-bordered table-hover" id="tblLogs">
            <thead>
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
        $liArr = array();

        foreach ($lines as $lneNum => $line) {
            $val = json_decode($line);
            $val->lineNum = $lneNum;
            $val->errorType = FriendlyErrorType($val->context->exception->level);
            array_push($liArr, $val);
        }
        ?>
        var jstr = '<?php echo addslashes(json_encode($liArr)) ?>';
        var jsonfile = JSON.parse(jstr);


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
                data: jsonfile,
                columns: [{
                        data: 'lineNum'
                    },
                    {
                        data: 'context.exception.error'
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
                "createdRow": function(row, data, dataIndex) {
                    $(row).addClass('clickable-row');
                    $(row).attr('data-href',"?id="+dataIndex );
                }
            });

            // Refilter the table
            $('#min, #max').on('change', function() {
                table.draw();
            });
            $('#tblLogs tbody').on('click', 'tr', function() {
        //$(this).toggleClass('selected');
        window.location = $(this).data("href");
    });
        });
    </script>
</div>