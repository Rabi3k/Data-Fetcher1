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

<button role="button" class="btn btn-info float-end" id="btn-save-texts"><i class="bi bi-save"></i> <?php _e("save_all", "Save All") ?></button>
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
                data: '_id',
                visible: false
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
            {
                render: function(data, type, row, meta) {
                    let btnDelete = '<button class="btn btn-outline-danger btn-delete"><i class="bi bi-trash"></i></button>';
                    let btnSave = '<button class="btn btn-outline-success btn-save"><i class="bi bi-save"></i></button>';
                    let spinner = '<span class="spinner spinner-border visually-hidden"></span>';
                    return `${btnDelete}${btnSave}${spinner}`;
                }
            }

        ],
    });
    $(document).ready(function() {
        $("table").on("click", ".btn-delete", function() {
            var parentTr = $(this).parents("tr");
            var trNode = tblTranslation.rows(parentTr).data()[0]
            var deleteTxt = `<?php _e("post_confirm_delete", "you are about to delete record") ?> '${trNode.text_key}'.\n<?php _e("confirm_delete", "Are you sure want to delete?") ?>`;
            if (confirm(deleteTxt)) {
                let spinner = $(parentTr).find(".spinner");
                $(spinner).removeClass("visually-hidden");

                let textData = {
                    "id": trNode._id,
                    "text": $(parentTr).find(".default-value").val(),
                    "language": "<?php echo $lang ?>",
                    "language_text": $(parentTr).find(".lang-value").val()
                };


                var settings = {
                    "url": "/sessionservices/texts.php?q=delete-text",
                    "method": "POST",
                    "timeout": 0,
                    "headers": {
                        "Content-Type": "application/json"
                    },
                    "data": JSON.stringify(textData),
                    "success": function(data) {
                        $(spinner).addClass("visually-hidden");
                        $("body").scrollTop(0)
                        showAlert("text deleted successfully");
                        tblTranslation.ajax.reload(null, false);
                        tblTranslation.draw(false);
                    }
                }

                $.ajax(settings).done(function(response) {});
                
            }

        });
        $("table").on("click", ".btn-save", function() {


            var parentTr = $(this).parents("tr");
            var trNode = tblTranslation.rows(parentTr).data()[0]

            let spinner = $(parentTr).find(".spinner");
            $(spinner).removeClass("visually-hidden");

            let textData = {
                "id": trNode._id,
                "text": $(parentTr).find(".default-value").val(),
                "language": "<?php echo $lang ?>",
                "language_text": $(parentTr).find(".lang-value").val()
            };


            var settings = {
                "url": "/sessionservices/texts.php?q=edit-text",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify(textData),
                "success": function(data) {
                    $(spinner).addClass("visually-hidden");
                    $("body").scrollTop(0)
                    showAlert("text saved successfully");
                }
            }

            $.ajax(settings).done(function(response) {});
        });
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

            var settings = {
                "url": "/sessionservices/texts.php?q=edit-text",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json"
                },
                "data": JSON.stringify(textData),
                "success": function(data) {
                    $(spinner).addClass("visually-hidden");
                    $("body").scrollTop(0)
                    showAlert("text saved successfully");
                }
            }

            $.ajax(settings).done(function(response) {});
        })


    });
</script>