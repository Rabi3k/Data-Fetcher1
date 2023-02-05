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


    thead th,
    tfoot th {
        font-family: OpenSans-Regular;
        font-size: 18px;
        color: #fff;
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
        border-bottom: 10px solid;
        border-image: linear-gradient(180deg, #007bff, #6c757d);
        border-image-slice: 220;
    }
</style>
<header class="customer-panel bg-primary text-light">
    <div class="container ">
        <div class="row">
            <div class="col-12 text-center">
                <span class="h2"><strong>Hvem er den næste</strong></span>
            </div>
        </div>
    </div>
</header>
<table id="example" class="" style="width:100%">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Navn</th>
            <th>Ordretype</th>
            <th>Til klar</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Order #</th>
            <th>Navn</th>
            <th>Ordretype</th>
            <th>Til klar</th>
        </tr>
    </tfoot>
</table>
<script>
    $(document).ready(function() {


        var table = $('#example').DataTable({
            
            scrollY: '60vh',
            scrollCollapse: true,
            fixedHeader: true,
            "language": {
                "emptyTable": "Ingen tilgængelige data i tabellen",
                "infoEmpty": "Viser 0 til 0 af 0 poster",
                "loadingRecords": "Indlæser...",
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
                    data: 'timeToEnd'
                },
            ],
        });
        setInterval(function() {
            table.ajax.reload();
            //table.draw();
        }, 2000);
    });
</script>