<!DOCTYPE html>
<?php
//echo "<span class='card'>".json_encode($data)." Test</span><br/>";
?>
<style>
    @font-face {
        font-family: OpenSans-Regular;
        src: url('../fonts/OpenSans/OpenSans-Regular.ttf');
    }

    table {
        border-spacing: 1;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        width: 100%;
        margin: 0 auto;
        position: relative;
    }

    table * {
        position: relative;
    }

    table td,
    table th {
        padding-left: 8px;
    }

    table thead tr,
    table tfoot tr {
        height: 60px;
        background: #36304a;
    }

    table tbody tr {
        height: 50px;
    }

    table tbody tr:last-child {
        border: 0;
    }

    table td,
    table th {
        text-align: left;
    }

    table td.l,
    table th.l {
        text-align: right;
    }

    table td.c,
    table th.c {
        text-align: center;
    }

    table td.r,
    table th.r {
        text-align: center;
    }

    .dataTables_filter {
        right: 1em;
        position: absolute;
    }

    li.paginate_button.page-item.active a {
        background-color: #36304a;
        border-color: #36304a;
        color: #DFAA0A;
    }

    thead th,
    tfoot th {
        font-family: OpenSans-Regular;
        font-size: 18px;
        color: #DFAA0A;
        line-height: 1.2;
        font-weight: unset;
    }

    tbody tr:nth-child(even) {
        background-color: #f5f5f5;
    }

    /* tr.odd>td {
        background-color: gainsboro;
        color: black
    }

    tr.even>td {
        background-color: white;
        color: black
    } */
    header.customer-panel {
        /* border-bottom: 10px solid; */
        /* border-image: linear-gradient(180deg, #DFAA0A, #6c757d); */
        /* border-image-slice: 220; */
        color: #DFAA0A;

    }
</style>
<header class="customer-panel bg-light p-2">
    <div class="container ">
        <div class="row">
            <div class="col-12 text-center">
                <span class="h2"><strong>Hvem er den næste?</strong></span>
            </div>
        </div>
    </div>
</header>
<table id="example" class="" style="width:100%">
    <thead class="fs-5 fw-bold">
        <tr>
            <th>Order #</th>
            <th>Navn</th>
            <th>Ordretype</th>
            <th>Bestilling klar</th>
            <th>Bestilling tid</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Order #</th>
            <th>Navn</th>
            <th>Ordretype</th>
            <th>Bestilling klar</th>
            <th>Bestilling tid</th>
        </tr>
    </tfoot>
</table>
<script>
    $(document).ready(function() {


        var table = $('#example').DataTable({

            scrollY: '60vh',
            scrollCollapse: true,
            fixedHeader: true,
            "pageLength": 50,
            "language": {
                "emptyTable": "Ingen tilgængelige data i tabellen",
                "infoEmpty": "Viser 0 til 0 af 0 poster",
                "info": "Viser _START_ til _END_ af _TOTAL_ poster",
                "loadingRecords": "Indlæser...",
                "search": "Søg:",
                "sLengthMenu": "Vis _MENU_ antal",
                "zeroRecords": "Ingen matchende registreringer fundet",
                "paginate": {
                    "first": "Først",
                    "last": "Sidst",
                    "next": "Næste",
                    "previous": "Tidligere"
                },
                "aria": {
                    "sortAscending": ": aktiver for at sortere kolonne stigende",
                    "sortDescending": ": aktiver for at sortere kolonne faldende"
                }
            },
            ajax: {
                url: 'main/create-card.php',
                dataType: 'json',
                type: 'POST',
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'client_name'
                },
                {
                    data: 'type'
                },
                {
                    data: 'timeToEnd',
                    className: "timeToEnd"
                },
                {
                    data: 'fulfill_at',
                    visible: false,
                    searchable: false
                },
            ],
            order: [
                [4, 'asc']
            ],
            "createdRow": function(row, data, dataIndex) {
                console.log(data);
                if (data["timeToEnd"] === "Nu") {
                    $(row).addClass('bg-success text-white fs-4 fw-bold');
                } else {
                    $(row).addClass('fs-5 fw-normal');
                }

            }
        });
        setInterval(function() {
            //table.ajax.reload();
            //table.draw();
        }, 2000);

    });
</script>