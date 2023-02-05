<!DOCTYPE html>
<?php
//echo "<span class='card'>".json_encode($data)." Test</span><br/>";
?>
<style>
    table {
        font-weight: bold;
        font-size: large;
        font-family: cursive;
    }

    table thead,
    table tfoot {
        background-color: red;
        color: white;
        border: 0px solid;
    }

    th,
    td {
        border: 5px dotted white;
    }

    tr.odd>td {
        background-color: gainsboro;
        color: black
    }

    tr.even>td {
        background-color: gainsboro;
        color: black
    }
</style>

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