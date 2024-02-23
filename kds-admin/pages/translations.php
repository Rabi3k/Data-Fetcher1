<?php
$allTexts = $textsStore->findAll(["_id" => "asc"]);
$lang = "da_DK";
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
}

$pattern = '{\w+_(?<flg>\w+)}';

if (preg_match($pattern, $lang, $matches)) {
    $flag = strtolower($matches['flg']);
} else {

    //echo ("User id: " . dechex(1984));
}
?>
<hr />

<nav class="navbar navbar-expand navbar-light bg-light">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Language
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?lang=da_DK"><span><i class="flag-icon flag-icon-dk"></i> Dansk</span></a></li>
                    <li><a class="dropdown-item" href="?lang=en_US"><span><i class="flag-icon flag-icon-us"></i> English</span></a></li>
                </ul>
            </li>

        </ul>
        <div class="d-flex">
            <span class="nav-text" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="flag-icon flag-icon-<?php echo $flag ?>"></i> <?php echo $flag == "dk" ? "Dansk" : "English" ?>
            </span>
        </div>
    </div>
</nav>

<hr />
<div class="">
    <table class="table" id="tbl-translation">
        <thead class="table-dark">
            <tr>
                <th scope="col">id</th>
                <th scope="col">Key</th>
                <th scope="col">Default Text</th>
                <th scope="col">Translated Text</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<hr />

<button role="button" class="btn btn-info float-end" id="btn-save-texts"><i class="bi bi-save"></i> Save</button>
<script>
    let tblTranslation = $('#tbl-translation').DataTable({
        responsive: true,
        ajax: {
            url: "/sessionservices/texts.php",
            dataType: 'json',
            type: 'GET',
        },
        //data: jsonfile,
        columns: [{
                data: '_id'
            },
            {
                data: 'text_key'
            },
            {
                data: 'text',
                render: function(data, type, row, meta) {
                    return '<input type="text" class="default-value" value="' + data + '">';
                }
            },
            {
                //data: 'languages["<?php echo $lang ?>"]',
                render: function(data, type, row, meta) {
                    let txt = "";
                    if (row.languages["<?php echo $lang ?>"]) {
                        txt = row.languages["<?php echo $lang ?>"].text ?? "";
                    }
                    return '<input type="text" class="lang-value" value="' + txt + '">';
                }
            },

        ],
        columnDefs: [{
            targets: 4,
            render: function(data, type, row, meta) {
                return '<span class="spinner spinner-border visually-hidden"></span>';
            }
        }],
    });
    $(document).ready(function() {


    });
    $("#btn-save-texts").click(function() {

        showAlert("hi");
        $(tblTranslation.rows().nodes()).each(function() {
            let spinner = $(this).find(".spinner");
            $(spinner).removeClass("visually-hidden");
            let textData = {
                "id": tblTranslation.rows(this).data()[0]._id,
                "text": $(this).find(".default-value").val(),
                "language": "<?php echo $lang ?>",
                "language_text": $(this).find(".lang-value").val()
            };
            console.log(JSON.stringify(textData));
            var settings = {
                "url": "/sessionservices/texts.php?q=edit-text",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify(textData),
                "success": function(data) {
                    console.log(data);
                    $(spinner).addClass("visually-hidden");
                    $("body").scrollTop(0)
                    showAlert("text saved successfully");
                }
            }

            $.ajax(settings).done(function(response) {
                console.log(response);

            });
        })


    });
</script>